<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class CjDropshippingService
{
    public static function getAccessToken()
    {
        return \Illuminate\Support\Facades\Cache::remember('cj_access_token', 86400, function () {
            $email = config('services.cj.email');
            $key = config('services.cj.key');
            if (!$email || !$key) return null;

            $response = Http::post('https://developers.cjdropshipping.com/api2.0/v1/authentication/getAccessToken', [
                'email' => $email,
                'password' => $key
            ]);

            if ($response->successful() && $response->json('result') === true) {
                return $response->json('data.accessToken');
            }
            return null;
        });
    }

    /**
     * Push an order to CJ Dropshipping API
     * # ponytail: Synchronous HTTP call. Add a Queue Job if CJ API becomes slow or times out.
     */
    public static function syncOrder(Order $order, $cartItems)
    {
        $token = self::getAccessToken();
        
        if (!$token) {
            Log::warning("CJ_API_TOKEN not set. Order {$order->order_number} not synced to CJ.");
            return false;
        }

        $shipping = json_decode($order->shipping_address, true);

        // Format products for CJ API
        $products = [];
        foreach ($cartItems as $productId => $item) {
            // In a real scenario, you'd look up the CJ SKU from your DB
            $products[] = [
                'vid' => 'CJ_VARIANT_ID_HERE', // Placeholder: needs actual CJ variant ID mapping
                'quantity' => $item['quantity']
            ];
        }

        $payload = [
            'orderNumber' => $order->order_number,
            'shippingZip' => $shipping['postal_code'] ?? '',
            'shippingCountry' => $shipping['country'] ?? '',
            'shippingProvince' => $shipping['state'] ?? '',
            'shippingCity' => $shipping['city'] ?? '',
            'shippingAddress' => ($shipping['address1'] ?? '') . ' ' . ($shipping['address2'] ?? ''),
            'shippingCustomerName' => ($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? ''),
            'shippingPhone' => $shipping['phone'] ?? '',
            'remark' => 'AtoZGadgets Automated Order',
            'fromCountryCode' => 'CN',
            'products' => $products
        ];

        try {
            $response = Http::withHeaders([
                'CJ-Access-Token' => $token,
                'Content-Type' => 'application/json'
            ])->post('https://developers.cjdropshipping.com/api2.0/v1/shoppingSync/synchronousOrder', $payload);

            if ($response->successful()) {
                Log::info("Order {$order->order_number} successfully synced to CJ Dropshipping.");
                return true;
            } else {
                Log::error("CJ API Error for Order {$order->order_number}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("CJ API Exception for Order {$order->order_number}: " . $e->getMessage());
            return false;
        }
    }
}
