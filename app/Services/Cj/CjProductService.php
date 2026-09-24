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
            return ['list' => $list, 'total' => count($list), 'success' => true];
        }

        try {
            if (!app()->environment('testing')) {
                usleep(1100000); // 1.1s throttle for CJ rate limits
            }
            
            $params = [
                'page' => (int)$pageNum,
                'size' => (int)$pageSize,
            ];

            if (!empty($keyword)) {
                $params['keyWord'] = $keyword;
            }
            if (!empty($filters['categoryId']) && strpos($filters['categoryId'], 'cj_cat_') === false) {
                $params['categoryId'] = $filters['categoryId'];
            }
            if (!empty($filters['countryCode'])) {
                $params['countryCode'] = strtoupper($filters['countryCode']);
            }
            if (isset($filters['minPrice']) && $filters['minPrice'] !== '' && (float)$filters['minPrice'] > 0) {
                $params['startSellPrice'] = (float)$filters['minPrice'];
            }
            if (isset($filters['maxPrice']) && $filters['maxPrice'] !== '' && (float)$filters['maxPrice'] > 0) {
                $params['endSellPrice'] = (float)$filters['maxPrice'];
            }

            // CJ V2 Elasticsearch Endpoint
            $response = Http::withHeaders(CjAuthService::getAuthHeaders())
                ->timeout(15)->retry(2, 300)
                ->get(self::getApiBaseUrl() . '/product/listV2', $params);

            $data = $response->json();

            if (isset($data['code']) && $data['code'] === 200 && !empty($data['data'])) {
                $rawData = $data['data'];
                $totalRecords = (int)($rawData['totalRecords'] ?? 0);
                $content = $rawData['content'] ?? [];

                $items = [];
                if (is_array($content)) {
                    foreach ($content as $block) {
                        if (isset($block['productList']) && is_array($block['productList'])) {
                            foreach ($block['productList'] as $p) {
                                $items[] = $p;
                            }
                        }
                    }
                }

                if (empty($items) && isset($rawData['list']) && is_array($rawData['list'])) {
                    $items = $rawData['list'];
                }

                if (count($items) > 0) {
                    $normalizedList = array_map(function($item) {
                        $rawImg = $item['bigImage'] ?? ($item['productImage'] ?? ($item['image'] ?? ''));
                        $rawPrice = $item['sellPrice'] ?? ($item['nowPrice'] ?? ($item['price'] ?? 0));
                        if (is_string($rawPrice)) {
                            if (str_contains($rawPrice, '--')) {
                                $parts = explode('--', $rawPrice);
                                $rawPrice = trim($parts[0]);
                            } elseif (str_contains($rawPrice, '-')) {
                                $parts = explode('-', $rawPrice);
                                $rawPrice = trim($parts[0]);
                            }
                            $rawPrice = preg_replace('/[^0-9.]/', '', $rawPrice);
                        }

                        return [
                            'pid' => (string)($item['id'] ?? ($item['pid'] ?? ($item['productId'] ?? ''))),
                            'productNameEn' => $item['nameEn'] ?? ($item['productNameEn'] ?? ($item['productName'] ?? '')),
                            'productSku' => $item['sku'] ?? ($item['productSku'] ?? ''),
                            'sellPrice' => (float)$rawPrice,
                            'productImage' => self::normalizeImageUrl($rawImg),
                            'categoryName' => $item['categoryName'] ?? ($item['threeCategoryName'] ?? 'Uncategorized'),
                            'productWeight' => $item['productWeight'] ?? null,
                        ];
                    }, $items);

                    Log::info("[CjProductService] Successfully fetched " . count($normalizedList) . " live products from CJ V2 API!");
                    return [
                        'list' => $normalizedList,
                        'total' => $totalRecords > 0 ? $totalRecords : count($normalizedList),
                        'success' => true
                    ];
                }

                return [
                    'list' => [],
                    'total' => 0,
                    'success' => true,
                    'message' => 'No products found matching your filter criteria.'
                ];
            }

            $msg = $data['message'] ?? 'CJ Dropshipping returned no items.';
            Log::warning('CJ Live Search Notice: ' . $msg, ['code' => $data['code'] ?? null]);
            return [
                'list' => [],
                'total' => 0,
                'message' => $msg,
                'success' => false
            ];

        } catch (\Exception $e) {
            Log::warning('CJ Live Search Exception: ' . $e->getMessage());
            return [
                'list' => [],
                'total' => 0,
                'message' => 'Network error connecting to CJ API: ' . $e->getMessage(),
                'success' => false
            ];
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

    public static function getCategoriesTree(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('cj_api_category_tree_v2', 86400, function () {
            $token = CjAuthService::getAccessToken();
            if ($token === 'SANDBOX_DEMO_TOKEN') {
                return self::getDemoCategoryTree();
            }

            try {
                $response = Http::withHeaders(CjAuthService::getAuthHeaders())
                    ->timeout(14)->retry(2, 200)
                    ->get(self::getApiBaseUrl() . '/product/getCategory');

                $data = $response->json();
                $list = $data['data'] ?? ($data['result'] ?? []);

                if (is_array($list) && !empty($list)) {
                    $tree = [];
                    foreach ($list as $first) {
                        $firstName = $first['categoryFirstName'] ?? ($first['categoryName'] ?? '');
                        $firstId = $first['categoryFirstId'] ?? ($first['categoryId'] ?? '');
                        if (empty($firstName) || empty($firstId)) continue;

                        $subs = [];
                        foreach ($first['categoryFirstList'] ?? [] as $second) {
                            $secondName = $second['categorySecondName'] ?? '';
                            $secondId = $second['categorySecondId'] ?? '';
                            if (empty($secondName) || empty($secondId)) continue;

                            $children = [];
                            foreach ($second['categorySecondList'] ?? [] as $third) {
                                $thirdName = $third['categoryName'] ?? '';
                                $thirdId = $third['categoryId'] ?? '';
                                if (!empty($thirdName) && !empty($thirdId)) {
                                    $children[] = [
                                        'id' => $thirdId,
                                        'name' => $thirdName,
                                    ];
                                }
                            }

                            $subs[] = [
                                'id' => $secondId,
                                'name' => $secondName,
                                'children' => $children,
                            ];
                        }

                        $tree[] = [
                            'id' => $firstId,
                            'name' => $firstName,
                            'subcategories' => $subs,
                        ];
                    }

                    if (!empty($tree)) {
                        return $tree;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('CJ Category Tree Fetch Error: ' . $e->getMessage());
            }

            return self::getDemoCategoryTree();
        });
    }

    private static function getDemoCategoryTree(): array
    {
        return [
            [
                'id' => 'cj_cat_electronics',
                'name' => 'Consumer Electronics',
                'subcategories' => [
                    [
                        'id' => 'cj_sub_audio',
                        'name' => 'Audio & Headphones',
                        'children' => [
                            ['id' => 'cj_sub_earbuds', 'name' => 'Wireless Earbuds'],
                            ['id' => 'cj_sub_speakers', 'name' => 'Bluetooth Speakers']
                        ]
                    ],
                    [
                        'id' => 'cj_sub_wearables',
                        'name' => 'Smart Wearables',
                        'children' => [
                            ['id' => 'cj_sub_smartwatches', 'name' => 'Smart Watches'],
                            ['id' => 'cj_sub_fitnessbands', 'name' => 'Fitness Trackers']
                        ]
                    ]
                ]
            ],
            [
                'id' => 'cj_cat_computer',
                'name' => 'Computer & Office',
                'subcategories' => [
                    [
                        'id' => 'cj_sub_accessories',
                        'name' => 'Office Supplies',
                        'children' => [
                            ['id' => 'cj_sub_stationery', 'name' => 'Whiteboards & Erasers'],
                            ['id' => 'cj_sub_mice', 'name' => 'Keyboards & Mice']
                        ]
                    ]
                ]
            ],
            [
                'id' => 'cj_cat_smarthome',
                'name' => 'Home, Garden & Furniture',
                'subcategories' => [
                    [
                        'id' => 'cj_sub_lighting',
                        'name' => 'Smart Lighting',
                        'children' => [
                            ['id' => 'cj_sub_lamps', 'name' => 'Desk & Night Lamps']
                        ]
                    ]
                ]
            ]
        ];
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
     * Clean and normalize any raw image URL from external suppliers.
     * Handles protocol-relative URLs (//...), escaped slashes, HTML entities, and formatting.
     */
    public static function normalizeImageUrl(?string $url): string
    {
        if (empty($url) || !is_string($url)) {
            return '';
        }

        $clean = trim($url, " \t\n\r\0\x0B\"'\\");
        $clean = str_replace(['\/', '&amp;'], ['/', '&'], $clean);

        if (str_starts_with($clean, '//')) {
            $clean = 'https:' . $clean;
        }

        if (!empty($clean) && (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://') || str_starts_with($clean, '/storage/'))) {
            return $clean;
        }

        return '';
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
            $normalized = self::normalizeImageUrl(is_string($item) ? $item : '');
            if (!empty($normalized) && !in_array($normalized, $clean)) {
                $clean[] = $normalized;
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
                // Extract all gallery images using robust extractor
                $rawImgField = $item['productImages'] ?? ($item['productImageSet'] ?? ($item['images'] ?? $item['productImage'] ?? ''));
                $extractedImages = self::extractImageList($rawImgField);
                
                // Ensure primary image is always included
                $mainImg = self::normalizeImageUrl($item['productImage'] ?? ($item['bigImage'] ?? ($extractedImages[0] ?? '')));
                if (!empty($mainImg) && !in_array($mainImg, $extractedImages)) {
                    array_unshift($extractedImages, $mainImg);
                }

                $variants = [];
                $rawVariants = $item['variants'] ?? ($item['variantList'] ?? []);
                foreach ($rawVariants as $v) {
                    $rawName = $v['variantNameEn'] ?? ($v['variantName'] ?? ($v['variantKey'] ?? ($v['variantStandard'] ?? '')));
                    if (empty(trim($rawName))) {
                        $values = array_filter([$v['variantValue1'] ?? null, $v['variantValue2'] ?? null, $v['variantValue3'] ?? null]);
                        $rawName = !empty($values) ? implode(' · ', $values) : '';
                    }

                    $vImg = self::normalizeImageUrl($v['variantImage'] ?? ($v['image'] ?? ($mainImg ?: '')));

                    $variants[] = [
                        'vid' => $v['vid'] ?? ($v['variantId'] ?? ('VID-' . uniqid())),
                        'variantSku' => $v['variantSku'] ?? ($v['sku'] ?? ''),
                        'variantName' => !empty(trim($rawName)) ? trim($rawName) : 'Standard Variant',
                        'variantKey' => $v['variantKey'] ?? null,
                        'variantStandard' => $v['variantStandard'] ?? null,
                        'costPrice' => (float)($v['variantSellPrice'] ?? ($v['price'] ?? ($item['sellPrice'] ?? 10.00))),
                        'image' => $vImg ?: $mainImg,
                        'inventory' => (int)($v['inventory'] ?? 100),
                        'option1_name' => $v['option1Name'] ?? ($v['option1_name'] ?? null),
                        'option1_value' => $v['option1Value'] ?? ($v['option1_value'] ?? null),
                        'option2_name' => $v['option2Name'] ?? ($v['option2_name'] ?? null),
                        'option2_value' => $v['option2Value'] ?? ($v['option2_value'] ?? null),
                    ];
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
