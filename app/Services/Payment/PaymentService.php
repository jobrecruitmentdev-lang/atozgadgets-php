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

    public static function createIntent(Order $order, string $provider = 'paypal'): array
    {
        $gateway = self::resolveGateway($provider);
        $orderRef = $order->order_number ?: 'ORD-' . $order->id;

        // Log Payment Attempt
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
        // 1. Idempotency Check: if order is already paid, return early
        if (in_array(strtolower($order->payment_status ?? ''), ['paid', 'completed', 'success'])) {
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
            $payment = Payment::where('order_id', $order->id)->where('status', 'success')->latest()->first();

            if (!$payment) {
                return ['success' => false, 'error' => 'No successful payment found to refund.'];
            }

            // Append REFUND transaction to immutable ledger
            $refundTx = PaymentTransaction::create([
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'type' => 'REFUND',
                'amount' => $refundAmount,
                'currency' => 'USD',
                'provider' => $payment->payment_method ?? 'paypal',
                'provider_transaction_id' => 'REF-' . Str::random(12),
                'status' => 'completed',
                'metadata' => ['reason' => $reason],
            ]);

            $payment->update([
                'status' => 'refunded',
            ]);

            $order->update([
                'status' => 'refunded',
                'payment_status' => 'refunded',
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

