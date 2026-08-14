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
        return env('CJ_API_BASE_URL', 'https://developers.cjdropshipping.com/api2.0/v1');
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
                'productNameEn' => $keyword,
                'pageNum' => $pageNum,
                'pageSize' => $pageSize,
            ];

            if (isset($filters['minPrice'])) $params['minPrice'] = $filters['minPrice'];
            if (isset($filters['maxPrice'])) $params['maxPrice'] = $filters['maxPrice'];
            if (isset($filters['categoryId'])) $params['categoryId'] = $filters['categoryId'];
            if (isset($filters['countryCode'])) $params['countryCode'] = $filters['countryCode'];

            $response = Http::withHeaders(CjAuthService::getAuthHeaders())
                ->timeout(10)->retry(3, 100)
                ->get(self::getApiBaseUrl() . '/product/list', $params);

            $data = $response->json();
            $rawData = $data['data'] ?? [];
            
            $list = $rawData['list'] ?? ($rawData['content'][0]['productList'] ?? ($rawData['content'] ?? []));
            $total = $rawData['totalRecords'] ?? ($rawData['total'] ?? (is_array($list) ? count($list) : 0));

            if (is_array($list) && count($list) > 0) {
                Log::info("[CjProductService] Successfully fetched " . count($list) . " live products from CJ API!");
                return ['list' => $list, 'total' => $total];
            }

            return ['list' => self::getDemoCatalog(), 'total' => count(self::getDemoCatalog())];

        } catch (\Exception $e) {
            Log::warning('CJ Live Search Warning, returning fallback demo catalog: ' . $e->getMessage());
            return ['list' => self::getDemoCatalog(), 'total' => count(self::getDemoCatalog())];
        }
    }
}
