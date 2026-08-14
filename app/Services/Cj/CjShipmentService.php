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
        return config('services.cj.api_url', 'https://developers.cjdropshipping.com/api2.0/v1') . $endpoint;
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
                $results[] = self::syncShipment($co->order_id);
            } catch (\Exception $e) {
                // Log error or continue
            }
        }
        
        return $results;
    }

    public static function handleWebhook(array $payload)
    {
        $orderNumber = $payload['orderNumber'] ?? null;
        $orderStatus = $payload['orderStatus'] ?? null;
        $trackingNumber = $payload['trackingNumber'] ?? null;
        $carrierName = $payload['carrierName'] ?? null;
        $trackingUrl = $payload['trackingUrl'] ?? null;

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) return;

        $cjOrder = CjOrder::where('internal_order_id', $order->id)->first();
        if (!$cjOrder) return;

        $cjOrder->update(['status' => $orderStatus]);

        if ($trackingNumber) {
            $shipment = DB::table('shipments')->where('order_id', $order->id)->first();
            if ($shipment) {
                // Removed insertion into cj_shipments as the table does not exist in schema

                DB::table('shipments')->where('id', $shipment->id)->update([
                    'tracking_number' => $trackingNumber,
                    'carrier' => $carrierName,
                    'status' => ($orderStatus === 'delivered') ? 'delivered' : 'shipped',
                    'updated_at' => now(),
                ]);
            }
        }

        $statusMap = [
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        if (isset($statusMap[$orderStatus])) {
            $order->update(['status' => strtolower($statusMap[$orderStatus])]);
        }
    }
}
