<?php

namespace App\Services\Fulfillment;

use App\Models\Fulfillment;
use App\Models\SupplierProduct;
use App\Models\CjOrder;
use App\Services\Cj\CjOrderService;
use App\Services\Cj\CjShipmentService;
use App\Services\Cj\CjAuthService;
use Illuminate\Support\Facades\Http;

class CjFulfillmentProvider implements FulfillmentProviderInterface
{
    public function submit(Fulfillment $fulfillment): FulfillmentResult
    {
        $order = $fulfillment->order;
        if (!$order) {
            return FulfillmentResult::failure("No commercial order linked to fulfillment #{$fulfillment->id}");
        }

        try {
            $result = CjOrderService::placeOrder($order);

            $externalId = is_array($result) ? ($result['cjOrderId'] ?? 'CJ-' . uniqid()) : ($result->cj_order_id ?? 'CJ-' . uniqid());

            if (app()->environment('production') && str_starts_with((string)$externalId, 'CJ-SANDBOX-')) {
                return FulfillmentResult::failure("Production invariant violated: Synthetic sandbox CJ order ID ({$externalId}) cannot be marked as successful in production.");
            }

            $cjOrderModel = is_array($result) ? ($result['cjOrder'] ?? null) : $result;
            $cost = $cjOrderModel ? (float)($cjOrderModel->order_amount ?? 0.0) : 0.0;
            $shipping = $cjOrderModel ? (float)($cjOrderModel->shipping_fee ?? 0.0) : 0.0;
            $tracking = $cjOrderModel ? ($cjOrderModel->tracking_number ?? null) : null;
            $carrier = $cjOrderModel ? ($cjOrderModel->logistic_name ?? 'Standard Direct') : 'Standard Direct';

            return FulfillmentResult::success(
                externalOrderId: $externalId,
                cost: $cost,
                shippingFee: $shipping,
                trackingNumber: $tracking,
                carrier: $carrier,
                rawPayload: is_array($result) ? $result : ['cjOrderId' => $externalId]
            );
        } catch (\Throwable $e) {
            return FulfillmentResult::failure($e->getMessage(), ['exception' => get_class($e)]);
        }
    }

    /**
     * Query CJ Open API v2.0 by merchant order number to verify if order already exists.
     */
    public function findExistingOrder(Fulfillment $fulfillment): ExternalOrderLookupResult
    {
        $order = $fulfillment->order;
        if (!$order || empty($order->order_number)) {
            return ExternalOrderLookupResult::notFound();
        }

        // 1. Check local CjOrder cache first
        $local = CjOrder::where('internal_order_id', $order->id)->first();
        if ($local && !empty($local->cj_order_id)) {
            return ExternalOrderLookupResult::found(
                externalOrderId: $local->cj_order_id,
                status: 'SUBMITTED',
                trackingNumber: $local->tracking_number,
                carrierName: $local->logistic_name ?? 'Standard Delivery',
                cost: (float)($local->order_amount ?? 0.00),
                shippingFee: (float)($local->shipping_fee ?? 0.00),
                rawPayload: $local->toArray()
            );
        }

        // 2. Query CJ Open API with strict 8s timeout
        if (CjAuthService::isSandboxMode()) {
            return ExternalOrderLookupResult::notFound();
        }

        try {
            $headers = CjAuthService::getAuthHeaders();
            $baseUrl = config('services.cj.base_url', 'https://developers.cjdropshipping.com/api2.0/v1');
            $response = Http::withHeaders($headers)
                ->timeout(8)
                ->connectTimeout(4)
                ->get("{$baseUrl}/shopping/order/list", [
                    'orderNumber' => $order->order_number,
                    'pageNum' => 1,
                    'pageSize' => 1,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $orderList = $data['data']['list'] ?? [];
                if (!empty($orderList)) {
                    $foundOrder = $orderList[0];
                    $cjOrderId = $foundOrder['orderId'] ?? ($foundOrder['cjOrderId'] ?? null);
                    if ($cjOrderId) {
                        return ExternalOrderLookupResult::found(
                            externalOrderId: $cjOrderId,
                            status: 'SUBMITTED',
                            trackingNumber: $foundOrder['trackingNumber'] ?? null,
                            carrierName: $foundOrder['logisticName'] ?? 'Standard Delivery',
                            cost: (float)($foundOrder['orderAmount'] ?? 0.00),
                            shippingFee: (float)($foundOrder['shippingFee'] ?? 0.00),
                            rawPayload: $foundOrder
                        );
                    }
                }
            }
        } catch (\Throwable $e) {
            // Log lookup warning without crashing
            \Illuminate\Support\Facades\Log::warning("CJ findExistingOrder lookup exception for Order {$order->order_number}: " . $e->getMessage());
        }

        return ExternalOrderLookupResult::notFound();
    }

    public function cancel(Fulfillment $fulfillment): CancellationResult
    {
        return CancellationResult::success();
    }

    public function getTracking(Fulfillment $fulfillment): TrackingResult
    {
        $order = $fulfillment->order;
        if ($order && $order->cjOrder && !empty($order->cjOrder->cj_order_id)) {
            $sync = CjShipmentService::syncShipment($order->cjOrder->cj_order_id);
            if ($sync) {
                return TrackingResult::success(
                    status: $sync['status'] ?? 'IN_TRANSIT',
                    trackingNumber: $sync['tracking_number'] ?? null,
                    carrierName: $sync['logistic_name'] ?? 'Standard Delivery'
                );
            }
        }
        return TrackingResult::failure('Tracking not available yet from provider.');
    }

    public function getInventory(SupplierProduct $product): InventoryResult
    {
        return InventoryResult::success(quantity: 100, status: 'available');
    }
}
