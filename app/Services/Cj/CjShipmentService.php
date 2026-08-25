<?php

namespace App\Services\Cj;

use App\Models\Order;
use App\Models\CjOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CjShipmentService
{
    protected static function getApiUrl($endpoint)
    {
        return config('services.cj.base_url', 'https://developers.cjdropshipping.com/api2.0/v1') . $endpoint;
    }

    public static function syncShipment($orderId)
    {
        $cjOrder = CjOrder::where('internal_order_id', $orderId)->first();
        if (!$cjOrder) {
            throw new \Exception("No CJ order found for order ID {$orderId}");
        }

        $headers = CjAuthService::getAuthHeaders();
        $response = Http::withHeaders($headers)
            ->get(self::getApiUrl('/logistic/order/list'), [
                'orderNum' => $cjOrder->cj_order_id
            ]);

        $data = $response->json();
        if ($data['code'] !== 200 || empty($data['data'])) {
            return null;
        }

        $tracking = $data['data'][0];

        $cjOrder->update([
            'status' => $tracking['orderStatus'] ?? 'in_transit'
        ]);

        $shipment = DB::table('shipments')->where('order_id', $orderId)->first();
        if (!$shipment) {
            return null;
        }

        // Removed insertion into cj_shipments as the table does not exist in schema

        $shippedAt = !empty($tracking['shippedAt']) ? Carbon::parse($tracking['shippedAt']) : null;
        $deliveredAt = ($tracking['orderStatus'] === 'delivered') ? now() : null;

        DB::table('shipments')->where('id', $shipment->id)->update([
            'tracking_number' => $tracking['trackingNumber'] ?? null,
            'carrier' => $tracking['carrierName'] ?? 'CJPacket',
            'status' => ($tracking['orderStatus'] === 'delivered') ? 'delivered' : 'shipped',
            'updated_at' => now(),
        ]);

        return DB::table('shipments')->where('id', $shipment->id)->first();
    }

    public static function syncAllActiveShipments()
    {
        $cjOrders = CjOrder::whereNotIn('status', ['delivered', 'cancelled'])->get();
        $results = [];
        
        foreach ($cjOrders as $co) {
            try {
                $results[] = self::syncShipment($co->internal_order_id);
            } catch (\Exception $e) {
                // Log error or continue
            }
        }
        
        return $results;
    }

    public static function handleWebhook(array $payload): bool
    {
        $orderNumber = $payload['orderNumber'] ?? null;
        $orderStatus = strtolower(trim($payload['orderStatus'] ?? ''));
        $trackingNumber = $payload['trackingNumber'] ?? null;
        $carrierName = $payload['carrierName'] ?? null;

        if (empty($orderNumber) || empty($orderStatus)) {
            return false;
        }

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return false;
        }

        $cjOrder = CjOrder::where('internal_order_id', $order->id)->first();
        if (!$cjOrder) {
            return false;
        }

        $currentOrderStatus = strtolower($order->status ?? 'pending');

        // Invariant: Completed or delivered orders CANNOT be cancelled by supplier webhook
        if (in_array($currentOrderStatus, ['delivered', 'completed']) && $orderStatus === 'cancelled') {
            \Illuminate\Support\Facades\Log::warning("CJ Webhook Invariant: Rejecting cancellation on completed/delivered order {$orderNumber}");
            return false;
        }

        // Invariant: Already cancelled or refunded orders CANNOT be moved to shipped/delivered via webhook
        if (in_array($currentOrderStatus, ['cancelled', 'refunded']) && in_array($orderStatus, ['shipped', 'delivered'])) {
            \Illuminate\Support\Facades\Log::warning("CJ Webhook Invariant: Rejecting shipment status on cancelled/refunded order {$orderNumber}");
            return false;
        }

        $cjOrder->update(['status' => $orderStatus]);

        if (!empty($trackingNumber)) {
            $shipment = DB::table('shipments')->where('order_id', $order->id)->first();
            if ($shipment) {
                DB::table('shipments')->where('id', $shipment->id)->update([
                    'tracking_number' => $trackingNumber,
                    'carrier' => $carrierName ?: 'CJPacket',
                    'status' => ($orderStatus === 'delivered') ? 'delivered' : 'shipped',
                    'updated_at' => now(),
                ]);
            }
        }

        $statusMap = [
            'shipped' => 'shipped',
            'delivered' => 'completed',
            'cancelled' => 'cancelled',
        ];

        if (isset($statusMap[$orderStatus])) {
            $order->update(['status' => $statusMap[$orderStatus]]);
        }

        return true;
    }
}
