<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\PaymentTransaction;
use App\Models\IdempotencyRecord;
use App\Services\Fraud\RiskService;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    private static function resolveGateway(string $provider = 'paypal'): PaymentGatewayInterface
    {
        return match (strtolower($provider)) {
            'stripe' => new StripeGateway(),
            default => new PayPalGateway(),
        };
    }

    /**
     * Compute authoritative payment ledger summary from immutable payment_transactions.
     */
    public static function getLedgerSummary(?Order $order): object
    {
        if (!$order) {
            return (object)[
                'captured' => 0.00,
                'refunded' => 0.00,
                'net_paid' => 0.00,
                'is_fully_paid' => false,
                'status' => 'UNPAID',
            ];
        }

        $captured = (float) PaymentTransaction::where('order_id', $order->id)
            ->where('type', 'CAPTURE')
            ->where('status', 'completed')
            ->sum('amount');

        $refunded = (float) PaymentTransaction::where('order_id', $order->id)
            ->whereIn('type', ['REFUND', 'PARTIAL_REFUND'])
            ->where('status', 'completed')
            ->sum('amount');

        // Fallback for payments recorded via Payment model without transaction ledger
        if ($captured <= 0 && $refunded <= 0) {
            $paymentCaptured = (float) Payment::where('order_id', $order->id)
                ->whereIn('status', ['success', 'completed'])
                ->sum('amount');
            if ($paymentCaptured > 0) {
                $captured = $paymentCaptured;
            }
        }

        $net = round($captured - $refunded, 2);
        $total = (float)$order->total_amount;

        $isFullyPaid = ($net >= $total && $captured > 0);

        $status = match (true) {
            $isFullyPaid => 'PAID',
            $refunded > 0 && $net > 0 => 'PARTIALLY_REFUNDED',
            $refunded > 0 && $net <= 0 => 'REFUNDED',
            $captured > 0 => 'PARTIALLY_PAID',
            default => 'PENDING',
        };

        return (object)[
            'captured' => $captured,
            'refunded' => $refunded,
            'net_paid' => $net,
            'is_fully_paid' => $isFullyPaid,
            'status' => $status,
        ];
    }

    public static function createIntent(Order $order, string $provider = 'paypal'): array
    {
        $gateway = self::resolveGateway($provider);
        $orderRef = $order->order_number ?: 'ORD-' . $order->id;

        $attempt = PaymentAttempt::create([
            'order_id' => $order->id,
            'provider' => $provider,
            'amount' => $order->total_amount,
            'currency' => 'USD',
            'status' => 'initiated',
            'idempotency_key' => 'intent_' . md5($order->id . '_' . time()),
        ]);

        $gatewayOrder = $gateway->createOrder($order->total_amount, $orderRef);

        $attempt->update([
            'provider_order_id' => $gatewayOrder['id'] ?? null,
            'status' => 'pending',
        ]);

        return $gatewayOrder;
    }

    public static function captureAndConfirm(Order $order, string $providerOrderId, string $provider = 'paypal', ?array $preCapturedData = null): array
    {
        // 1. Check if order ledger already records complete capture
        $ledger = self::getLedgerSummary($order);
        if ($ledger->is_fully_paid) {
            return [
                'success' => true,
                'message' => 'Order is already marked as paid.',
                'order_id' => $order->id,
            ];
        }

        if ($preCapturedData) {
            $captureData = $preCapturedData;
        } else {
            $gateway = self::resolveGateway($provider);
            $captureData = $gateway->captureOrder($providerOrderId);
        }

        $status = $captureData['status'] ?? 'UNKNOWN';
        if ($status !== 'COMPLETED' && $status !== 'succeeded') {
            PaymentAttempt::where('order_id', $order->id)
                ->where('provider_order_id', $providerOrderId)
                ->update([
                    'status' => 'failed',
                    'failure_code' => $status,
                    'failure_message' => json_encode($captureData),
                ]);

            return [
                'success' => false,
                'error' => 'Payment capture not completed.',
                'details' => $captureData,
            ];
        }

        $captureUnit = $captureData['purchase_units'][0]['payments']['captures'][0] ?? null;
        $capturedAmount = (float)($captureUnit['amount']['value'] ?? ($captureData['amount']['value'] ?? $order->total_amount));
        $capturedCurrency = $captureUnit['amount']['currency_code'] ?? ($captureData['amount']['currency_code'] ?? 'USD');
        $captureId = $captureUnit['id'] ?? ($captureData['id'] ?? 'CAP-' . Str::random(12));

        // 2. Risk & Amount Verification
        $assessment = RiskService::evaluate($order, $capturedAmount, $capturedCurrency);
        if ($assessment->decision === 'REJECT') {
            return [
                'success' => false,
                'error' => 'Payment rejected due to risk and amount mismatch flags.',
                'risk_assessment' => $assessment,
            ];
        }

        // 3. Atomically Record Payment & Append-Only Transaction Ledger
        DB::transaction(function () use ($order, $provider, $providerOrderId, $captureId, $capturedAmount, $capturedCurrency, $captureData) {
            $payment = Payment::firstOrCreate(
                [
                    'order_id' => $order->id,
                    'transaction_id' => $captureId,
                ],
                [
                    'payment_method' => $provider,
                    'amount' => $capturedAmount,
                    'status' => 'success',
                ]
            );

            // Append to Immutable Transaction Ledger (Idempotent by provider_transaction_id)
            PaymentTransaction::firstOrCreate(
                ['provider_transaction_id' => $captureId],
                [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'type' => 'CAPTURE',
                    'amount' => $capturedAmount,
                    'currency' => $capturedCurrency,
                    'provider' => $provider,
                    'status' => 'completed',
                    'metadata' => $captureData,
                ]
            );

            // Mark Order as Paid (triggers Inventory Confirm & Outbox Event)
            OrderService::markAsPaid($order);
        });

        return [
            'success' => true,
            'message' => 'Payment captured and order verified successfully.',
            'order_id' => $order->id,
            'transaction_id' => $captureId,
        ];
    }

    public static function processRefund(Order $order, ?float $amount = null, string $reason = 'Customer Request'): array
    {
        $refundAmount = $amount ?? (float)$order->total_amount;

        return DB::transaction(function () use ($order, $refundAmount, $reason) {
            $payment = Payment::where('order_id', $order->id)
                ->whereIn('status', ['success', 'partially_refunded'])
                ->latest()
                ->first();

            if (!$payment) {
                return ['success' => false, 'error' => 'No successful payment found to refund.'];
            }

            $txType = ($refundAmount >= (float)$order->total_amount) ? 'REFUND' : 'PARTIAL_REFUND';

            // Append REFUND transaction to immutable ledger
            $refundTx = PaymentTransaction::create([
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'type' => $txType,
                'amount' => $refundAmount,
                'currency' => 'USD',
                'provider' => $payment->payment_method ?? 'paypal',
                'provider_transaction_id' => 'REF-' . Str::random(12),
                'status' => 'completed',
                'metadata' => ['reason' => $reason],
            ]);

            // Update projection
            $ledger = self::getLedgerSummary($order);
            $payment->update(['status' => ($ledger->net_paid <= 0 ? 'refunded' : 'partially_refunded')]);
            $order->update([
                'status' => ($ledger->net_paid <= 0 ? 'refunded' : 'partially_refunded'),
                'payment_status' => strtolower($ledger->status),
            ]);

            return [
                'success' => true,
                'message' => 'Refund processed successfully.',
                'refund_id' => $refundTx->provider_transaction_id,
                'amount' => $refundAmount,
            ];
        });
    }
}
