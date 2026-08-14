<?php

namespace App\Services\Cj;

use App\Models\Order;
use App\Models\CjOrder;
use Illuminate\Support\Facades\Http;

class CjOrderService
{
    protected static function getApiUrl($endpoint)
    {
        return config('services.cj.api_url', 'https://developers.cjdropshipping.com/api2.0/v1') . $endpoint;
    }

    public static function placeOrder($orderId)
    {
        $order = Order::with(['items.product.cjProduct', 'user', 'address'])->findOrFail($orderId);

        $headers = CjAuthService::getAuthHeaders();

        $products = [];
        foreach ($order->items as $item) {
            if ($item->product->fulfillment_type === 'cj' && $item->product->cjProduct) {
                $products[] = [
                    'vid' => $item->product->cjProduct->cj_product_id, // Default to product ID
                    'quantity' => $item->quantity,
                ];
            }
        }

        if (empty($products)) {
            throw new \Exception('No CJ-fulfillable items in this order.');
        }

        $countryCode = stripos($order->address->country, 'india') !== false ? 'IN' : 'US';

        $payload = [
            'orderNumber' => $order->order_number,
            'shippingCountryCode' => $countryCode,
            'shippingAddress' => $order->address->address_line1,
            'shippingAddress2' => $order->address->address_line2 ?? '',
            'shippingZip' => $order->address->postal_code,
            'shippingPhone' => $order->user->mobile,
            'shippingCustomerName' => trim($order->user->first_name . ' ' . $order->user->last_name),
            'shippingCity' => $order->address->city,
            'shippingProvince' => $order->address->state,
            'products' => $products,
        ];

        $response = Http::withHeaders($headers)
            ->post(self::getApiUrl('/shopping/order/createOrder'), $payload);

        $responseData = $response->json();

        if ($responseData['code'] !== 200) {
            throw new \Exception('CJ order creation failed: ' . json_encode($responseData));
        }

        $cjOrderId = $responseData['data']['orderId'];

        CjOrder::create([
            'internal_order_id' => $orderId,
            'cj_order_id' => $cjOrderId,
            'status' => 'created',
        ]);

        self::submitOrder($cjOrderId, $headers);

        return ['cjOrderId' => $cjOrderId];
    }

    private static function submitOrder($cjOrderId, $headers)
    {
        $response = Http::withHeaders($headers)
            ->post(self::getApiUrl('/shopping/order/submitOrder'), [
                'orderId' => $cjOrderId
            ]);
        return $response->json();
    }

    public static function cancelOrder($cjOrderId)
    {
        $headers = CjAuthService::getAuthHeaders();
        $response = Http::withHeaders($headers)
            ->post(self::getApiUrl('/shopping/order/cancelOrder'), [
                'orderId' => $cjOrderId
            ]);
            
        $data = $response->json();
        return $data['code'] === 200;
    }

    public static function getOrderDetail($cjOrderId)
    {
        $headers = CjAuthService::getAuthHeaders();
        $response = Http::withHeaders($headers)
            ->get(self::getApiUrl('/shopping/order/getOrderDetail'), [
                'orderId' => $cjOrderId
            ]);
            
        $data = $response->json();
        return $data['data'] ?? null;
    }
}
