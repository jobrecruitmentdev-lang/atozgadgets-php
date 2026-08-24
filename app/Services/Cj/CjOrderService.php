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
        $order = ($orderId instanceof Order) 
            ? $orderId->loadMissing(['items.product.cjProduct', 'items.product.variants', 'items.variant', 'user', 'orderAddress']) 
            : Order::with(['items.product.cjProduct', 'items.product.variants', 'items.variant', 'user', 'orderAddress'])->findOrFail($orderId);

        // 1. Idempotency Check: Prevent duplicate CJ submissions
        $existingCjOrder = CjOrder::where('internal_order_id', $order->id)
            ->whereIn('status', ['submitted', 'created', 'shipped', 'delivered'])
            ->first();
        if ($existingCjOrder) {
            return ['cjOrderId' => $existingCjOrder->cj_order_id, 'cjOrder' => $existingCjOrder];
        }

        $headers = CjAuthService::getAuthHeaders();

        $products = [];
        foreach ($order->items as $item) {
            if ($item->product && $item->product->fulfillment_type === 'cj') {
                $vid = self::resolveVariantId($item);
                $products[] = [
                    'vid' => $vid,
                    'quantity' => $item->quantity,
                ];
            }
        }

        if (empty($products)) {
            throw new \Exception('No CJ-fulfillable items in this order.');
        }

        $address = $order->orderAddress 
            ?: ($order->address 
            ?: (is_string($order->shipping_address) ? json_decode($order->shipping_address) : (is_array($order->shipping_address) ? (object)$order->shipping_address : null)));

        $countryCode = CjAddressNormalizer::normalizeCountryCode($address->country ?? ($address->country_code ?? 'US'));

        // Fetch best logistics method
        $logisticName = self::getBestLogistic($countryCode, $products, $headers);

        $customerName = trim(($address->first_name ?? ($order->user->first_name ?? 'Customer')) . ' ' . ($address->last_name ?? ($order->user->last_name ?? '')));
        $rawPhone = $address->phone ?? ($order->user->mobile ?? ($order->contact_phone ?? ''));
        $customerPhone = CjAddressNormalizer::cleanPhone($rawPhone);
        if (empty($customerPhone)) {
            $customerPhone = '1000000000';
        }

        $payload = [
            'orderNumber' => $order->order_number,
            'fromCountryCode' => 'CN', // Defaulting to China warehouse
            'logisticName' => $logisticName,
            'shippingCountryCode' => $countryCode,
            'shippingAddress' => $address->address_line1 ?? ($address->address1 ?? 'Address Line 1'),
            'shippingAddress2' => $address->address_line2 ?? ($address->address2 ?? ''),
            'shippingZip' => $address->postal_code ?? ($address->zip ?? '00000'),
            'shippingPhone' => $customerPhone,
            'shippingCustomerName' => $customerName ?: 'Customer',
            'shippingCity' => $address->city ?? 'City',
            'shippingProvince' => $address->state ?? 'State',
            'products' => $products,
        ];

        if (CjAuthService::isSandboxMode() || ($headers['CJ-Access-Token'] ?? '') === 'SANDBOX_DEMO_TOKEN') {
            $cjOrderId = 'CJ-SANDBOX-' . strtoupper(uniqid());
            $cjOrder = CjOrder::updateOrCreate(
                ['internal_order_id' => $order->id],
                [
                    'cj_order_id' => $cjOrderId,
                    'status' => 'submitted',
                    'order_amount' => $order->total_amount,
                    'shipping_fee' => 5.99,
                    'tracking_number' => 'CJTRACK' . rand(10000000, 99999999) . 'US',
                    'logistic_name' => 'CJPacket Fast Line',
                ]
            );
            return ['cjOrderId' => $cjOrderId, 'cjOrder' => $cjOrder];
        }

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

        if (CjOrder::where('cj_order_id', $cjOrderId)->where('internal_order_id', '!=', $order->id)->exists()) {
            $cjOrderId .= '-' . substr(md5(uniqid()), 0, 6);
        }

        $cjOrder = CjOrder::updateOrCreate(
            ['internal_order_id' => $order->id],
            [
                'cj_order_id' => $cjOrderId,
                'status' => 'created',
            ]
        );

        $submitRes = self::submitOrder($cjOrderId, $headers);
        if (isset($submitRes['code']) && $submitRes['code'] !== 200) {
            $msg = $submitRes['message'] ?? 'CJ Wallet payment/deduction failed. Please check CJ account balance.';
            \Illuminate\Support\Facades\Log::warning("CJ Order {$cjOrderId} submitOrder returned code {$submitRes['code']}: {$msg}");
        }

        return ['cjOrderId' => $cjOrderId, 'cjOrder' => $cjOrder];
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

    public static function resolveVariantId($item): string
    {
        // 1. Direct variant on item
        if (!empty($item->variant_id)) {
            $variant = $item->variant ?: \App\Models\ProductVariant::find($item->variant_id);
            if ($variant && !empty($variant->cj_variant_id)) {
                return (string)$variant->cj_variant_id;
            }
        }

        // 2. First product variant with cj_variant_id
        if ($item->product && $item->product->variants && $item->product->variants->isNotEmpty()) {
            $firstVariant = $item->product->variants->first();
            if (!empty($firstVariant->cj_variant_id)) {
                return (string)$firstVariant->cj_variant_id;
            }
        }

        // 3. Fallback to cjProduct
        if ($item->product && $item->product->cjProduct) {
            return (string)($item->product->cjProduct->cj_variant_id 
                ?? $item->product->cjProduct->cj_product_id 
                ?? ($item->product->sku ?: 'VID-' . $item->product->id));
        }

        return (string)($item->product->sku ?? ('VID-' . ($item->product_id ?? $item->id)));
    }
}

