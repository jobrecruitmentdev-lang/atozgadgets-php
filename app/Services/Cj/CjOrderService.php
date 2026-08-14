<?php

namespace App\Services\Cj;

use App\Models\Order;
use App\Models\CjOrder;
use Illuminate\Support\Facades\Http;

class CjOrderService
{
    protected static function getApiUrl($endpoint)
    {
        return config('services.cj.base_url', 'https://developers.cjdropshipping.com/api2.0/v1') . $endpoint;
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

        // Fetch best logistics method
        $logisticName = self::getBestLogistic($countryCode, $products, $headers);

        $payload = [
            'orderNumber' => $order->order_number,
            'fromCountryCode' => 'CN', // Defaulting to China warehouse
            'logisticName' => $logisticName,
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
            ->post(self::getApiUrl('/shopping/order/createOrderV2'), $payload);

        $responseData = $response->json();

        if (!isset($responseData['code']) || $responseData['code'] !== 200) {
            throw new \Exception('CJ order creation failed: ' . json_encode($responseData));
        }

        $cjOrderId = $responseData['data']['orderId'] ?? ($responseData['data'] ?? null);

        if (!$cjOrderId) {
            throw new \Exception('CJ order creation failed. No orderId returned. ' . json_encode($responseData));
        }

        CjOrder::create([
            'internal_order_id' => $orderId,
            'cj_order_id' => $cjOrderId,
            'status' => 'created',
        ]);

        self::submitOrder($cjOrderId, $headers);

        return ['cjOrderId' => $cjOrderId];
    }

    private static function getBestLogistic($countryCode, $products, $headers)
    {
        // Simple fallback if no specific logistics found
        $fallback = 'CJPacket Fast Line';
        
        try {
            $response = Http::withHeaders($headers)
                ->post(self::getApiUrl('/logistic/freightCalculate'), [
                    'startCountryCode' => 'CN',
                    'endCountryCode' => $countryCode,
                    'products' => $products
                ]);
            
            $data = $response->json();
            if (isset($data['code']) && $data['code'] === 200 && !empty($data['data'])) {
                // Usually sorted, pick the first one or a known reliable one
                return $data['data'][0]['logisticName'] ?? $fallback;
            }
        } catch (\Exception $e) {
            // Log warning but proceed with fallback
            \Illuminate\Support\Facades\Log::warning('CJ Freight calculation failed: ' . $e->getMessage());
        }
        
        return $fallback;
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
