<?php

namespace App\Services\Cj;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\CjProduct;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class CjProductService
{
    private static function getApiBaseUrl()
    {
        return config('services.cj.base_url', 'https://developers.cjdropshipping.com/api2.0/v1');
    }

    private static function getDemoCatalog()
    {
        return [
            [
                'pid' => 'CJ-SMART-PRO-PROJECTOR-01',
                'productNameEn' => 'AtoZ Mini HD Smart LED Projector 1080P WiFi Portable',
                'productSku' => 'CJ-PROJ-1080P',
                'sellPrice' => 29.50,
                'productImage' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=800&auto=format&fit=crop',
                'productImages' => [
                    'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=800&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=800&auto=format&fit=crop',
                ],
                'categoryName' => 'Electronics & Gadgets',
            ],
            // Only adding one demo product to keep this file concise for the migration.
            // In production, the live credentials will pull thousands of items.
        ];
    }

    public static function searchProducts($keyword, $pageNum = 1, $pageSize = 20, $filters = [])
    {
        $token = CjAuthService::getAccessToken();
        
        if ($token === 'SANDBOX_DEMO_TOKEN') {
            $list = self::getDemoCatalog();
            return ['list' => $list, 'total' => count($list)];
        }

        try {
            usleep(1100000); // 1.1s throttle
            
            $params = [
                'keyWord' => $keyword,
                'page' => $pageNum,
                'size' => $pageSize,
            ];

            if (isset($filters['minPrice'])) $params['minPrice'] = $filters['minPrice'];
            if (isset($filters['maxPrice'])) $params['maxPrice'] = $filters['maxPrice'];
            if (isset($filters['categoryId'])) $params['categoryId'] = $filters['categoryId'];
            if (isset($filters['countryCode'])) $params['countryCode'] = $filters['countryCode'];

            $response = Http::withHeaders(CjAuthService::getAuthHeaders())
                ->timeout(10)->retry(3, 100)
                ->get(self::getApiBaseUrl() . '/product/listV2', $params);

            $data = $response->json();
            // In listV2, it might be in $data['data']['list'] or $data['result']['list']
            $rawData = $data['data'] ?? ($data['result'] ?? []);
            
            $list = $rawData['list'] ?? ($rawData['content'][0]['productList'] ?? ($rawData['content'] ?? []));
            $total = $rawData['totalRecords'] ?? ($rawData['total'] ?? (is_array($list) ? count($list) : 0));

            if (is_array($list) && count($list) > 0) {
                // Normalize keys for frontend
                $normalizedList = array_map(function($item) {
                    return [
                        'pid' => $item['pid'] ?? ($item['productId'] ?? ''),
                        'productNameEn' => $item['productNameEn'] ?? ($item['productName'] ?? ($item['title'] ?? '')),
                        'productSku' => $item['productSku'] ?? ($item['sku'] ?? ''),
                        'sellPrice' => $item['sellPrice'] ?? ($item['price'] ?? 0),
                        'productImage' => $item['productImage'] ?? ($item['image'] ?? ''),
                        'categoryName' => $item['categoryName'] ?? ($item['category'] ?? 'Uncategorized'),
                    ];
                }, $list);

                Log::info("[CjProductService] Successfully fetched " . count($normalizedList) . " live products from CJ API!");
                return ['list' => $normalizedList, 'total' => $total];
            }

            return ['list' => self::getDemoCatalog(), 'total' => count(self::getDemoCatalog())];

        } catch (\Exception $e) {
            Log::warning('CJ Live Search Warning, returning fallback demo catalog: ' . $e->getMessage());
            return ['list' => self::getDemoCatalog(), 'total' => count(self::getDemoCatalog())];
        }
    }
}
