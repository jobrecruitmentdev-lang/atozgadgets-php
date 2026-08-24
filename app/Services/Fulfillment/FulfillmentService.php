<?php

namespace App\Services\Fulfillment;

use App\Models\Order;
use App\Models\Fulfillment;
use App\Models\FulfillmentItem;
use App\Models\FulfillmentAttempt;
use App\Models\FulfillmentException;
use App\Models\FulfillmentProvider;
use App\Models\Shipment;
use App\Models\ShipmentCarrier;
use App\Models\SupplierOrder;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FulfillmentService
{
    public function __construct(private ?FulfillmentProviderInterface $customProvider = null) {}

    /**
     * Resolve the provider adapter by code or model.
     */
    public static function resolveProvider(?string $code = 'cj'): FulfillmentProviderInterface
    {
        return match (strtolower($code ?? 'cj')) {
            'cj' => new CjFulfillmentProvider(),
            default => new CjFulfillmentProvider(),
        };
    }

    /**
     * Create initial pending fulfillment record(s) for a paid order.
     */
    public static function createFulfillmentsForOrder(Order $order): Fulfillment
    {
        $provider = FulfillmentProvider::where('code', 'cj')->first();

        return DB::transaction(function () use ($order, $provider) {
            $fulfillment = Fulfillment::create([
                'order_id' => $order->id,
                'provider_id' => $provider?->id,
                'fulfillment_status' => 'PENDING',
                'notes' => 'Auto-queued for fulfillment upon payment capture',
            ]);

            foreach ($order->items as $item) {
                FulfillmentItem::create([
                    'fulfillment_id' => $fulfillment->id,
                    'order_item_id' => $item->id,
                    'quantity' => $item->quantity,
                    'status' => 'PENDING',
                ]);
            }

            return $fulfillment;
        });
    }

    /**
     * Instance method to fulfill an order (supporting mock provider injection).
     */
    public function fulfillOrder(Order $order): Fulfillment
    {
        $fulfillment = self::createFulfillmentsForOrder($order);
        
        if ($this->customProvider) {
            self::executeWithAdapter($fulfillment, $this->customProvider);
        } else {
            self::executeFulfillment($fulfillment);
        }

        return $fulfillment->fresh();
    }

    /**
     * Execute fulfillment with strict idempotency and attempt logging.
     */
    public static function executeFulfillment(Fulfillment $fulfillment): FulfillmentResult
    {
        $providerCode = $fulfillment->provider?->code ?? 'cj';
        $adapter = self::resolveProvider($providerCode);

        return self::executeWithAdapter($fulfillment, $adapter);
    }

    /**
     * Execute fulfillment using specified adapter with query-before-retry reconciliation.
     */
    public static function executeWithAdapter(Fulfillment $fulfillment, FulfillmentProviderInterface $adapter): FulfillmentResult
    {
        $order = $fulfillment->order;

        // Invariant 1: Authoritative Payment Ledger Verification
        $ledger = PaymentService::getLedgerSummary($order);
        if (!$order || !$ledger->is_fully_paid) {
            $err = "Order #{$order?->order_number} payment is not fully captured in ledger (Net Paid: \${$ledger->net_paid}, Total: \${$order?->total_amount}).";
            self::recordException($fulfillment, 'UNPAID_ORDER', $err);
            return FulfillmentResult::failure($err);
        }

        // Invariant 2 & 3: Supplier Reconciliation Protocol (Query-Before-Retry)
        if ($fulfillment->fulfillment_status === 'RECONCILIATION_REQUIRED' || $fulfillment->attempts()->count() > 0) {
            $lookup = $adapter->findExistingOrder($fulfillment);
            if ($lookup->found && !empty($lookup->externalOrderId)) {
                // Order already exists on supplier; adopt it without duplicate submit
                return self::adoptReconciledOrder($fulfillment, $lookup);
            }
        }

        // Idempotency Tracking
        $attemptCount = $fulfillment->attempts()->count() + 1;
        $idempotencyKey = "AZG-ORD-{$order->order_number}:FULFILL:{$fulfillment->id}:{$attemptCount}";

        $attempt = FulfillmentAttempt::create([
            'fulfillment_id' => $fulfillment->id,
            'idempotency_key' => $idempotencyKey,
            'attempt_number' => $attemptCount,
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
        ]);

        $fulfillment->update(['fulfillment_status' => 'SUBMITTING']);

        try {
            $result = $adapter->submit($fulfillment);
        } catch (\Throwable $e) {
            $isTimeout = str_contains(strtolower($e->getMessage()), 'timeout') || str_contains(strtolower($e->getMessage()), 'timed out');
            
            $attempt->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            $newStatus = $isTimeout ? 'RECONCILIATION_REQUIRED' : 'EXCEPTION';
            $fulfillment->update(['fulfillment_status' => $newStatus]);
            self::recordException($fulfillment, $isTimeout ? 'TIMEOUT' : 'SUPPLIER_ERROR', $e->getMessage());

            return FulfillmentResult::failure($e->getMessage());
        }

        if ($result->success) {
            $attempt->update([
                'status' => 'SUCCESS',
                'response_payload' => json_encode($result->rawPayload),
                'completed_at' => now(),
            ]);

            $fulfillment->update(['fulfillment_status' => 'SUBMITTED']);
            $order->update(['status' => 'processing']);

            // Create or update white-labeled shipment
            $carrier = ShipmentCarrier::where('internal_code', 'standard')->first();
            Shipment::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'fulfillment_id' => $fulfillment->id,
                    'carrier_id' => $carrier?->id,
                    'tracking_number' => $result->trackingNumber,
                    'carrier_name' => $result->carrier ?? 'Standard Delivery',
                    'customer_carrier_name' => $carrier?->customer_name ?? 'Standard Delivery',
                    'status' => $result->trackingNumber ? 'SHIPPED' : 'NOT_SHIPPED',
                    'shipped_at' => $result->trackingNumber ? now() : null,
                ]
            );

            // Record internal supplier order cost ledger
            if ($result->externalOrderId) {
                SupplierOrder::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'supplier' => $fulfillment->provider?->code ?? 'cj',
                        'external_order_id' => $result->externalOrderId,
                        'product_cost' => $result->cost,
                        'shipping_cost' => $result->shippingFee,
                        'total_cost' => $result->cost + $result->shippingFee,
                        'status' => 'submitted',
                        'raw_response' => json_encode($result->rawPayload),
                    ]
                );
            }

            return $result;
        }

        // Handle Failure
        $isTimeout = str_contains(strtolower($result->errorMessage ?? ''), 'timeout');
        $attempt->update([
            'status' => 'FAILED',
            'error_message' => $result->errorMessage,
            'response_payload' => json_encode($result->rawPayload),
            'completed_at' => now(),
        ]);

        $newStatus = $isTimeout ? 'RECONCILIATION_REQUIRED' : 'EXCEPTION';
        $fulfillment->update(['fulfillment_status' => $newStatus]);
        self::recordException($fulfillment, $isTimeout ? 'TIMEOUT' : 'PROVIDER_REJECTED', $result->errorMessage ?? 'Unknown provider error');

        return $result;
    }

    /**
     * Adopt an existing supplier order found during reconciliation.
     */
    private static function adoptReconciledOrder(Fulfillment $fulfillment, ExternalOrderLookupResult $lookup): FulfillmentResult
    {
        $order = $fulfillment->order;

        $fulfillment->update(['fulfillment_status' => 'SUBMITTED']);
        $order->update(['status' => 'processing']);

        // Log adoption attempt
        $attemptCount = $fulfillment->attempts()->count() + 1;
        FulfillmentAttempt::create([
            'fulfillment_id' => $fulfillment->id,
            'idempotency_key' => "AZG-ORD-{$order->order_number}:RECONCILE:{$attemptCount}",
            'attempt_number' => $attemptCount,
            'status' => 'SUCCESS',
            'response_payload' => json_encode($lookup->rawPayload),
            'completed_at' => now(),
        ]);

        $carrier = ShipmentCarrier::where('internal_code', 'standard')->first();
        Shipment::firstOrCreate(
            ['order_id' => $order->id],
            [
                'fulfillment_id' => $fulfillment->id,
                'carrier_id' => $carrier?->id,
                'tracking_number' => $lookup->trackingNumber,
                'carrier_name' => $lookup->carrierName ?? 'Standard Delivery',
                'customer_carrier_name' => $carrier?->customer_name ?? 'Standard Delivery',
                'status' => $lookup->trackingNumber ? 'SHIPPED' : 'NOT_SHIPPED',
            ]
        );

        SupplierOrder::updateOrCreate(
            ['order_id' => $order->id],
            [
                'supplier' => $fulfillment->provider?->code ?? 'cj',
                'external_order_id' => $lookup->externalOrderId,
                'product_cost' => $lookup->cost,
                'shipping_cost' => $lookup->shippingFee,
                'total_cost' => $lookup->cost + $lookup->shippingFee,
                'status' => 'submitted',
                'raw_response' => json_encode($lookup->rawPayload),
            ]
        );

        return FulfillmentResult::success(
            externalOrderId: $lookup->externalOrderId,
            cost: $lookup->cost,
            shippingFee: $lookup->shippingFee,
            trackingNumber: $lookup->trackingNumber,
            carrier: $lookup->carrierName,
            rawPayload: $lookup->rawPayload
        );
    }

    /**
     * Record an exception in the fulfillment dead-letter ledger.
     */
    public static function recordException(Fulfillment $fulfillment, string $code, string $message, array $context = []): FulfillmentException
    {
        return FulfillmentException::create([
            'fulfillment_id' => $fulfillment->id,
            'error_code' => $code,
            'error_message' => $message,
            'context_payload' => $context,
            'resolution_status' => 'OPEN',
        ]);
    }
}
