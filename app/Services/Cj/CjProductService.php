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
                'countryCode' => 'US',
            ],
            [
                'pid' => 'CJ-WIRELESS-LAMP-02',
                'productNameEn' => 'AtoZ 3-in-1 Fast Wireless Charging Station LED Desk Lamp',
                'productSku' => 'CJ-LAMP-3IN1',
                'sellPrice' => 14.80,
                'productImage' => 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?q=80&w=800&auto=format&fit=crop',
                'productImages' => [
                    'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?q=80&w=800&auto=format&fit=crop',
                ],
                'categoryName' => 'Smart Home',
                'countryCode' => 'US',
            ],
            [
                'pid' => 'CJ-RGB-ORB-SPEAKER-03',
                'productNameEn' => 'AtoZ Magnetic Levitation Floating Bluetooth Speaker RGB',
                'productSku' => 'CJ-FLOAT-SPK',
                'sellPrice' => 34.20,
                'productImage' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?q=80&w=800&auto=format&fit=crop',
                'productImages' => [
                    'https://images.unsplash.com/photo-1545454675-3531b543be5d?q=80&w=800&auto=format&fit=crop',
                ],
                'categoryName' => 'Audio & Sound',
                'countryCode' => 'CN',
            ],
            [
                'pid' => 'CJ-4K-MINI-DRONE-04',
                'productNameEn' => 'AtoZ 4K Ultra HD Foldable Mini Drone with Obstacle Avoidance',
                'productSku' => 'CJ-DRONE-4K',
                'sellPrice' => 42.00,
                'productImage' => 'https://images.unsplash.com/photo-1527977966376-1c8408f9f108?q=80&w=800&auto=format&fit=crop',
                'productImages' => [
                    'https://images.unsplash.com/photo-1527977966376-1c8408f9f108?q=80&w=800&auto=format&fit=crop',
                ],
                'categoryName' => 'Drones & Toys',
                'countryCode' => 'US',
            ],
            [
                'pid' => 'CJ-SWEATSHIRT-COUPLE-05',
                'productNameEn' => 'AtoZ Couple & Parent-Child Matching Premium Cotton Sweatshirt',
                'productSku' => 'CJ-SWEAT-SET',
                'sellPrice' => 19.90,
                'productImage' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?q=80&w=800&auto=format&fit=crop',
                'productImages' => [
                    'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?q=80&w=800&auto=format&fit=crop',
                ],
                'categoryName' => 'Couple & Parent-Child Sweatshirts',
                'countryCode' => 'US',
            ],
            [
                'pid' => 'CJ-SMART-BOTTLE-06',
                'productNameEn' => 'AtoZ Digital Temperature Display Smart Vacuum Flask 500ml',
                'productSku' => 'CJ-BOTTLE-LED',
                'sellPrice' => 8.90,
                'productImage' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?q=80&w=800&auto=format&fit=crop',
                'productImages' => [
                    'https://images.unsplash.com/photo-1602143407151-7111542de6e8?q=80&w=800&auto=format&fit=crop',
                ],
                'categoryName' => 'Home & Kitchen',
                'countryCode' => 'US',
            ],
            [
                'pid' => 'CJ-PORTABLE-BLENDER-07',
                'productNameEn' => 'AtoZ USB Rechargeable Personal Smoothie Juicer Blender 6 Blades',
                'productSku' => 'CJ-BLENDER-USB',
                'sellPrice' => 11.50,
                'productImage' => 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?q=80&w=800&auto=format&fit=crop',
                'productImages' => [
                    'https://images.unsplash.com/photo-1570222094114-d054a817e56b?q=80&w=800&auto=format&fit=crop',
                ],
                'categoryName' => 'Home & Kitchen',
                'countryCode' => 'CN',
            ],
            [
                'pid' => 'CJ-FITNESS-WATCH-08',
                'productNameEn' => 'AtoZ Waterproof AMOLED Smart Fitness Watch with Heart Rate & GPS',
                'productSku' => 'CJ-WATCH-AMOLED',
                'sellPrice' => 24.99,
                'productImage' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?q=80&w=800&auto=format&fit=crop',
                'productImages' => [
                    'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?q=80&w=800&auto=format&fit=crop',
                ],
                'categoryName' => 'Wearable Tech',
                'countryCode' => 'US',
            ],
        ];
    }

    public static function searchProducts($keyword = '', $pageNum = 1, $pageSize = 20, $filters = [])
    {
        $token = CjAuthService::getAccessToken();
        
        if ($token === 'SANDBOX_DEMO_TOKEN') {
            $catalog = self::getDemoCatalog();
            $filtered = array_filter($catalog, function($item) use ($keyword, $filters) {
                // Keyword match
                if (!empty($keyword)) {
                    $kw = strtolower($keyword);
                    $nameMatch = str_contains(strtolower($item['productNameEn'] ?? ''), $kw);
                    $catMatch = str_contains(strtolower($item['categoryName'] ?? ''), $kw);
                    if (!$nameMatch && !$catMatch) {
                        return false;
                    }
                }
                // Price match
                $price = (float)($item['sellPrice'] ?? 0);
                if (isset($filters['minPrice']) && $filters['minPrice'] !== '' && $price < (float)$filters['minPrice']) {
                    return false;
                }
                if (isset($filters['maxPrice']) && $filters['maxPrice'] !== '' && $price > (float)$filters['maxPrice']) {
                    return false;
                }
                return true;
            });

            $list = array_values(!empty($filtered) ? $filtered : $catalog);
            return ['list' => $list, 'total' => count($list)];
        }

        try {
            if (!app()->environment('testing')) {
                usleep(1100000); // 1.1s throttle for CJ rate limits
            }
            
            $params = [
                'pageNum' => $pageNum,
                'pageSize' => $pageSize,
            ];

            if (!empty($keyword)) {
                $params['productName'] = $keyword;
            }
            if (!empty($filters['categoryId']) && strpos($filters['categoryId'], 'cj_cat_') === false) {
                $params['categoryId'] = $filters['categoryId'];
            }
            // If neither keyword nor category is provided, default to 'gadget'
            if (empty($params['productName']) && empty($params['categoryId'])) {
                $params['productName'] = 'gadget';
            }
            if (!empty($filters['countryCode'])) {
                $params['countryCode'] = $filters['countryCode'];
            }
            if (isset($filters['minPrice']) && $filters['minPrice'] !== '') {
                $params['startSellPrice'] = (float)$filters['minPrice'];
            }
            if (isset($filters['maxPrice']) && $filters['maxPrice'] !== '') {
                $params['endSellPrice'] = (float)$filters['maxPrice'];
            }

            $response = Http::withHeaders(CjAuthService::getAuthHeaders())
                ->timeout(12)->retry(2, 200)
                ->get(self::getApiBaseUrl() . '/product/list', $params);

            $data = $response->json();
            $rawData = $data['data'] ?? ($data['result'] ?? []);
            $list = $rawData['list'] ?? [];
            $total = $rawData['total'] ?? (is_array($list) ? count($list) : 0);

            if (is_array($list) && count($list) > 0) {
                // Normalize keys for frontend
                $normalizedList = array_map(function($item) {
                    return [
                        'pid' => $item['pid'] ?? ($item['id'] ?? ($item['productId'] ?? '')),
                        'productNameEn' => $item['productNameEn'] ?? ($item['productName'] ?? ($item['nameEn'] ?? '')),
                        'productSku' => $item['productSku'] ?? ($item['sku'] ?? ''),
                        'sellPrice' => (float)($item['sellPrice'] ?? ($item['price'] ?? 0)),
                        'productImage' => $item['productImage'] ?? ($item['bigImage'] ?? ($item['image'] ?? '')),
                        'categoryName' => $item['categoryName'] ?? 'Uncategorized',
                        'productWeight' => $item['productWeight'] ?? null,
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

    public static function getCategories(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('cj_api_categories_v2', 86400, function () {
            $token = CjAuthService::getAccessToken();
            if ($token === 'SANDBOX_DEMO_TOKEN') {
                return self::getDemoCategories();
            }

            try {
                $response = Http::withHeaders(CjAuthService::getAuthHeaders())
                    ->timeout(12)->retry(2, 200)
                    ->get(self::getApiBaseUrl() . '/product/getCategory');

                $data = $response->json();
                $list = $data['data'] ?? ($data['result'] ?? []);

                if (is_array($list) && !empty($list)) {
                    $categories = [];
                    foreach ($list as $first) {
                        $firstName = $first['categoryFirstName'] ?? ($first['categoryName'] ?? '');
                        $firstId = $first['categoryFirstId'] ?? ($first['categoryId'] ?? '');
                        if (!empty($firstName) && !empty($firstId)) {
                            $categories[] = [
                                'id' => $firstId,
                                'name' => $firstName,
                                'level' => 1
                            ];
                        }
                        foreach ($first['categoryFirstList'] ?? [] as $second) {
                            $secondName = $second['categorySecondName'] ?? '';
                            $secondId = $second['categorySecondId'] ?? '';
                            if (!empty($secondName) && !empty($secondId)) {
                                $categories[] = [
                                    'id' => $secondId,
                                    'name' => " — {$secondName}",
                                    'level' => 2
                                ];
                            }
                            foreach ($second['categorySecondList'] ?? [] as $third) {
                                $thirdName = $third['categoryName'] ?? '';
                                $thirdId = $third['categoryId'] ?? '';
                                if (!empty($thirdName) && !empty($thirdId)) {
                                    $categories[] = [
                                        'id' => $thirdId,
                                        'name' => "   ↳ {$thirdName}",
                                        'level' => 3
                                    ];
                                }
                            }
                        }
                    }
                    if (!empty($categories)) {
                        return $categories;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('CJ Live Category Fetch Warning: ' . $e->getMessage());
            }

            return self::getDemoCategories();
        });
    }

    private static function getDemoCategories(): array
    {
        return [
            ['id' => 'cj_cat_electronics', 'name' => 'Consumer Electronics'],
            ['id' => 'cj_cat_smarthome', 'name' => 'Smart Home & Wearables'],
            ['id' => 'cj_cat_audio', 'name' => 'Audio, Sound & Headsets'],
            ['id' => 'cj_cat_drones', 'name' => 'Cameras, Drones & Optics'],
            ['id' => 'cj_cat_gaming', 'name' => 'Computer & Gaming Accessories'],
            ['id' => 'cj_cat_phones', 'name' => 'Mobile Phones & Tablets'],
            ['id' => 'cj_cat_appliances', 'name' => 'Smart Living & Kitchen Appliances'],
            ['id' => 'cj_cat_fitness', 'name' => 'Sports, Fitness & Outdoor Gadgets'],
        ];
    }

    /**
     * Parse and extract all valid image URLs from any input format:
     * - JSON string array (e.g. '["https://...1.jpg", "https://...2.jpg"]')
     * - Comma or whitespace separated string
     * - Native PHP array
     */
    public static function extractImageList(mixed $rawImages): array
    {
        if (empty($rawImages)) {
            return [];
        }

        $list = [];

        if (is_array($rawImages)) {
            $list = $rawImages;
        } elseif (is_string($rawImages)) {
            $trimmed = trim($rawImages);
            // 1. Check if it's a JSON array string
            if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    $list = $decoded;
                }
            }

            // 2. If not JSON, check comma/newline separated
            if (empty($list)) {
                $parts = preg_split('/[\r\n,]+/', $trimmed);
                $list = array_filter(array_map('trim', $parts));
            }
        }

        // Clean, validate and deduplicate URLs
        $clean = [];
        foreach ($list as $item) {
            if (is_string($item)) {
                $url = trim($item, " \t\n\r\0\x0B\"'\\");
                if (!empty($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '/storage/'))) {
                    if (!in_array($url, $clean)) {
                        $clean[] = $url;
                    }
                }
            }
        }

        return $clean;
    }

    public static function getProductDetails($pid): array
    {
        $token = CjAuthService::getAccessToken();

        if ($token === 'SANDBOX_DEMO_TOKEN') {
            return self::getDemoProductDetails($pid);
        }

        try {
            if (!app()->environment('testing')) {
                usleep(1100000); // 1.1s throttle
            }

            $response = Http::withHeaders(CjAuthService::getAuthHeaders())
                ->timeout(12)->retry(2, 200)
                ->get(self::getApiBaseUrl() . '/product/query', ['pid' => $pid]);

            $data = $response->json();
            $item = $data['data'] ?? ($data['result'] ?? []);

            if (!empty($item)) {
                $variants = [];
                $rawVariants = $item['variants'] ?? ($item['variantList'] ?? []);
                foreach ($rawVariants as $v) {
                    $variants[] = [
                        'vid' => $v['vid'] ?? ($v['variantId'] ?? ('VID-' . uniqid())),
                        'variantSku' => $v['variantSku'] ?? ($v['sku'] ?? ''),
                        'variantName' => $v['variantNameEn'] ?? ($v['variantName'] ?? ($v['variantKey'] ?? 'Default Variant')),
                        'costPrice' => (float)($v['variantSellPrice'] ?? ($v['price'] ?? ($item['sellPrice'] ?? 10.00))),
                        'image' => $v['variantImage'] ?? ($item['productImage'] ?? ''),
                        'inventory' => (int)($v['variantStandard'] ?? ($v['inventory'] ?? 100)),
                        'option1_name' => $v['option1Name'] ?? null,
                        'option1_value' => $v['option1Value'] ?? null,
                        'option2_name' => $v['option2Name'] ?? null,
                        'option2_value' => $v['option2Value'] ?? null,
                    ];
                }

                // Extract all gallery images using robust extractor
                $rawImgField = $item['productImages'] ?? ($item['productImageSet'] ?? ($item['images'] ?? $item['productImage'] ?? ''));
                $extractedImages = self::extractImageList($rawImgField);
                
                // Ensure primary image is always included
                $mainImg = $item['productImage'] ?? ($item['bigImage'] ?? ($extractedImages[0] ?? ''));
                if (!empty($mainImg) && !in_array($mainImg, $extractedImages)) {
                    array_unshift($extractedImages, $mainImg);
                }

                return [
                    'pid' => $item['pid'] ?? $pid,
                    'nameEn' => $item['productNameEn'] ?? ($item['productName'] ?? 'CJ Gadget Item'),
                    'sku' => $item['productSku'] ?? ('CJ-SKU-' . strtoupper(substr(md5($pid), 0, 8))),
                    'sellPrice' => (float)($item['sellPrice'] ?? 15.00),
                    'mainImage' => $mainImg,
                    'images' => $extractedImages,
                    'description' => $item['description'] ?? ($item['productNameEn'] ?? ''),
                    'categoryName' => $item['categoryName'] ?? 'Consumer Electronics',
                    'variants' => $variants,
                ];
            }
        } catch (\Exception $e) {
            Log::warning("CJ Live Details Fetch Warning for PID {$pid}: " . $e->getMessage());
        }

        return self::getDemoProductDetails($pid);
    }

    private static function getDemoProductDetails($pid): array
    {
        return [
            'pid' => $pid,
            'nameEn' => 'AtoZ Smart Gadget Pro Edition',
            'sku' => 'CJ-GADGET-' . strtoupper(substr(md5($pid), 0, 6)),
            'sellPrice' => 19.50,
            'mainImage' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?q=80&w=800&auto=format&fit=crop',
            'images' => [
                'https://images.unsplash.com/photo-1546868871-7041f2a55e12?q=80&w=800&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=800&auto=format&fit=crop',
            ],
            'description' => 'Premium high-performance smart gadget designed for modern lifestyle and maximum convenience.',
            'categoryName' => 'Consumer Electronics',
            'variants' => [
                [
                    'vid' => 'CJ-VID-BLK-64G-' . substr(md5($pid), 0, 8),
                    'variantSku' => 'CJ-VAR-BLK',
                    'variantName' => 'Midnight Black / Standard',
                    'costPrice' => 19.50,
                    'image' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?q=80&w=800&auto=format&fit=crop',
                    'inventory' => 250,
                    'option1_name' => 'Color',
                    'option1_value' => 'Midnight Black',
                    'option2_name' => 'Edition',
                    'option2_value' => 'Standard',
                ],
                [
                    'vid' => 'CJ-VID-SLV-128G-' . substr(md5($pid), 0, 8),
                    'variantSku' => 'CJ-VAR-SLV',
                    'variantName' => 'Titanium Silver / Pro',
                    'costPrice' => 24.50,
                    'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=800&auto=format&fit=crop',
                    'inventory' => 180,
                    'option1_name' => 'Color',
                    'option1_value' => 'Titanium Silver',
                    'option2_name' => 'Edition',
                    'option2_value' => 'Pro',
                ]
            ]
        ];
    }
}
