<?php

namespace App\Services\Cj;

use App\Models\Order;
use App\Models\SupplierOrder;
use App\Models\Setting;
use App\Services\Order\SupplierAdapterInterface;
use Illuminate\Support\Facades\Log;

class CjSupplierAdapter implements SupplierAdapterInterface
{
    public function fulfill(Order $order): SupplierOrder
    {
        return self::fulfillOrder($order);
    }

    public function cancel(string $externalOrderId): bool
    {
        return CjOrderService::cancelOrder($externalOrderId);
    }

    public function track(string $externalOrderId): ?array
    {
        return CjShipmentService::syncShipment($externalOrderId);
    }

    public static function fulfillOrder(Order $order): SupplierOrder
    {
        // 1. Create or retrieve existing SupplierOrder record
        $supplierOrder = SupplierOrder::firstOrCreate(
            [
                'order_id' => $order->id,
                'supplier' => 'cj',
            ],
            [
                'status' => 'submitting',
                'currency' => 'USD',
                'total_cost' => 0.00,
            ]
        );

        try {
            // 2. Delegate to CjOrderService (which handles sandbox fallback, ISO country normalization, and API 2.0 placement)
            $cjOrderResult = CjOrderService::placeOrder($order);

            $order->update([
                'fulfillment_status' => 'submitted',
                'status' => 'processing',
            ]);

            $externalOrderId = is_array($cjOrderResult) ? ($cjOrderResult['cjOrderId'] ?? 'CJ-' . uniqid()) : ($cjOrderResult->cj_order_id ?? 'CJ-' . uniqid());
            $cjOrderModel = is_array($cjOrderResult) ? ($cjOrderResult['cjOrder'] ?? null) : $cjOrderResult;
            $productCost = $cjOrderModel ? ($cjOrderModel->order_amount ?? 0.00) : 0.00;
            $shippingCost = $cjOrderModel ? ($cjOrderModel->shipping_fee ?? 0.00) : 0.00;
            $trackingNumber = $cjOrderModel ? ($cjOrderModel->tracking_number ?? null) : null;
            $carrierName = $cjOrderModel ? ($cjOrderModel->logistic_name ?? 'CJPacket Fast Line') : 'CJPacket Fast Line';

            $supplierOrder->update([
                'external_order_id' => $externalOrderId,
                'status' => 'submitted',
                'product_cost' => $productCost,
                'shipping_cost' => $shippingCost,
                'total_cost' => $productCost + $shippingCost,
                'tracking_number' => $trackingNumber,
                'carrier_name' => $carrierName,
                'submitted_at' => now(),
            ]);

            return $supplierOrder;
        } catch (\Throwable $e) {
            $order->update([
                'fulfillment_status' => 'failed',
            ]);

            $supplierOrder->update([
                'status' => 'failed',
                'failure_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
