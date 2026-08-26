<?php

namespace App\Services\Shipping;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Services\Cj\CjAddressNormalizer;
use App\Services\Cj\CjAuthService;
use App\Services\Cj\CjOrderService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CjShippingEligibilityService
{
    /**
     * Tier-1 Global Fast Delivery Corridors Supported by AtoZGadgets
     */
    const TIER1_CORRIDORS = [
        'US' => ['name' => 'United States', 'default_carrier' => 'CJPacket Fast Line / USPS', 'eta' => '3–7 Days (US Warehouse) / 7–10 Days (Express)'],
        'GB' => ['name' => 'United Kingdom', 'default_carrier' => 'CJPacket UK / Royal Mail', 'eta' => '6–10 Business Days'],
        'CA' => ['name' => 'Canada', 'default_carrier' => 'CJPacket Canada / Canada Post', 'eta' => '7–12 Business Days'],
        'AU' => ['name' => 'Australia', 'default_carrier' => 'CJPacket Australia / Australia Post', 'eta' => '7–12 Business Days'],
        'DE' => ['name' => 'Germany', 'default_carrier' => 'CJ Frankfurt Hub / DHL Express', 'eta' => '3–7 Days (EU) / 7–10 Days'],
        'FR' => ['name' => 'France', 'default_carrier' => 'CJPacket Europe / La Poste', 'eta' => '7–11 Business Days'],
        'NL' => ['name' => 'Netherlands', 'default_carrier' => 'CJPacket Europe / PostNL', 'eta' => '6–10 Business Days'],
        'IT' => ['name' => 'Italy', 'default_carrier' => 'CJPacket Europe / Poste Italiane', 'eta' => '7–12 Business Days'],
        'ES' => ['name' => 'Spain', 'default_carrier' => 'CJPacket Europe / Correos', 'eta' => '7–12 Business Days'],
        'NZ' => ['name' => 'New Zealand', 'default_carrier' => 'CJPacket NZ / NZ Post', 'eta' => '8–12 Business Days'],
    ];

    /**
     * Resolve Cart Items to CJ Variant IDs and Quantities
     */
    public static function resolveCartProducts(array $rawCart): array
    {
        $products = [];

        foreach ($rawCart as $key => $item) {
            $productId = is_numeric($key) ? (int)$key : ($item['product_id'] ?? null);
            $variantId = $item['variant_id'] ?? null;

            $product = $productId ? Product::with(['variants', 'cjProduct'])->find($productId) : null;
            $variant = $variantId ? ProductVariant::find($variantId) : null;

            $qty = max(1, (int)($item['quantity'] ?? 1));

            // Resolve VID
            $vid = null;
            if ($variant && !empty($variant->cj_variant_id)) {
                $vid = (string)$variant->cj_variant_id;
            } elseif ($product) {
                $dummyItem = (object)[
                    'product' => $product,
                    'variant_id' => $variantId,
                    'variant' => $variant,
                    'quantity' => $qty,
                ];
                $vid = CjOrderService::resolveVariantId($dummyItem);
            }

            if (!$vid) {
                $vid = (string)($item['sku'] ?? 'VID-' . ($productId ?: rand(1000, 9999)));
            }

            $products[] = [
                'vid' => $vid,
                'quantity' => $qty,
            ];
        }

        return $products;
    }

    /**
     * Authoritative Verification: Check if cart items can be shipped to destination country
     */
    public static function checkEligibility(array $rawCart, string $countryCode): array
    {
        $country = CjAddressNormalizer::normalizeCountryCode(trim($countryCode));
        $countryName = self::TIER1_CORRIDORS[$country]['name'] ?? $country;

        if (empty($rawCart)) {
            return [
                'eligible' => false,
                'country' => $country,
                'country_name' => $countryName,
                'carrier' => null,
                'eta' => null,
                'warehouse' => null,
                'message' => 'Your cart is empty.',
            ];
        }

        $products = self::resolveCartProducts($rawCart);

        // Check cache (1 hour ttl to avoid QPS rate limits)
        $cacheKey = 'cj_freight_eligibility_' . $country . '_' . md5(json_encode($products));
        $cachedResult = Cache::get($cacheKey);
        if ($cachedResult !== null) {
            return $cachedResult;
        }

        $headers = CjAuthService::getAuthHeaders();
        $isSandboxOrTesting = CjAuthService::isSandboxMode() || app()->environment(['local', 'testing']) || ($headers['CJ-Access-Token'] ?? '') === 'SANDBOX_DEMO_TOKEN';

        // 1. In Local / Testing / Sandbox mode
        if ($isSandboxOrTesting) {
            if (isset(self::TIER1_CORRIDORS[$country])) {
                $corridor = self::TIER1_CORRIDORS[$country];
                $result = [
                    'eligible' => true,
                    'country' => $country,
                    'country_name' => $corridor['name'],
                    'carrier' => $corridor['default_carrier'],
                    'eta' => $corridor['eta'],
                    'warehouse' => ($country === 'US') ? 'US East & West Fulfillment Hub' : 'Priority International Air Hub',
                    'shipping_fee' => 0.00,
                    'message' => "Express delivery available to {$corridor['name']} via {$corridor['default_carrier']} ({$corridor['eta']}).",
                ];
            } else {
                $result = [
                    'eligible' => false,
                    'country' => $country,
                    'country_name' => $countryName,
                    'carrier' => null,
                    'eta' => null,
                    'warehouse' => null,
                    'shipping_fee' => null,
                    'message' => "Shipping is currently unavailable to {$countryName} for one or more items in your cart due to regional carrier restrictions.",
                ];
            }

            Cache::put($cacheKey, $result, now()->addMinutes(60));
            return $result;
        }

        // 2. Real Live CJ Freight Calculation Query
        try {
            $apiUrl = config('services.cj.base_url', 'https://developers.cjdropshipping.com/api2.0/v1') . '/logistic/freightCalculate';
            
            $response = CjOrderService::executeWithRetry(function () use ($headers, $country, $products, $apiUrl) {
                return Http::withHeaders($headers)
                    ->timeout(10)
                    ->post($apiUrl, [
                        'startCountryCode' => 'CN',
                        'endCountryCode' => $country,
                        'products' => $products,
                    ]);
            });

            $data = $response->json();

            if (isset($data['code']) && $data['code'] === 200 && !empty($data['data'])) {
                $bestMethod = $data['data'][0];
                $carrierName = $bestMethod['logisticName'] ?? (self::TIER1_CORRIDORS[$country]['default_carrier'] ?? 'CJPacket Fast Line');
                $logisticAging = $bestMethod['logisticAging'] ?? (self::TIER1_CORRIDORS[$country]['eta'] ?? '7–12 Business Days');

                $result = [
                    'eligible' => true,
                    'country' => $country,
                    'country_name' => $countryName,
                    'carrier' => $carrierName,
                    'eta' => is_numeric($logisticAging) ? "{$logisticAging} Business Days" : $logisticAging,
                    'warehouse' => 'Priority Global Distribution Hub',
                    'shipping_fee' => 0.00,
                    'message' => "Express delivery available to {$countryName} via {$carrierName}.",
                ];
            } else {
                // No logistics lines available for this country
                $result = [
                    'eligible' => false,
                    'country' => $country,
                    'country_name' => $countryName,
                    'carrier' => null,
                    'eta' => null,
                    'warehouse' => null,
                    'shipping_fee' => null,
                    'message' => "Shipping is currently unavailable to {$countryName} for one or more items in your cart.",
                ];
            }
        } catch (\Throwable $e) {
            Log::warning("CJ Shipping eligibility query failed for {$country}: " . $e->getMessage());
            
            // Fallback for Tier-1 Corridors
            if (isset(self::TIER1_CORRIDORS[$country])) {
                $corridor = self::TIER1_CORRIDORS[$country];
                $result = [
                    'eligible' => true,
                    'country' => $country,
                    'country_name' => $corridor['name'],
                    'carrier' => $corridor['default_carrier'],
                    'eta' => $corridor['eta'],
                    'warehouse' => 'Priority Global Distribution Hub',
                    'shipping_fee' => 0.00,
                    'message' => "Express delivery available to {$corridor['name']}.",
                ];
            } else {
                $result = [
                    'eligible' => false,
                    'country' => $country,
                    'country_name' => $countryName,
                    'carrier' => null,
                    'eta' => null,
                    'warehouse' => null,
                    'shipping_fee' => null,
                    'message' => "Shipping unavailable to {$countryName}.",
                ];
            }
        }

        Cache::put($cacheKey, $result, now()->addMinutes(60));
        return $result;
    }
}
