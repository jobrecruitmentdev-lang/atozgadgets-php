<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\CjProduct;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    private $demoCatalog = [
        [
            'pid' => 'CJ-SMART-PRO-PROJECTOR-01',
            'productNameEn' => 'AtoZ Mini HD Smart LED Projector 1080P WiFi Portable',
            'productSku' => 'CJ-PROJ-1080P',
            'sellPrice' => 29.50,
            'productImage' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Electronics & Gadgets',
        ],
        [
            'pid' => 'CJ-WIRELESS-LAMP-02',
            'productNameEn' => 'AtoZ 3-in-1 Fast Wireless Charging Station LED Desk Lamp',
            'productSku' => 'CJ-LAMP-3IN1',
            'sellPrice' => 14.80,
            'productImage' => 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Smart Home',
        ],
        [
            'pid' => 'CJ-RGB-ORB-SPEAKER-03',
            'productNameEn' => 'AtoZ Magnetic Levitation Floating Bluetooth Speaker RGB',
            'productSku' => 'CJ-FLOAT-SPK',
            'sellPrice' => 34.20,
            'productImage' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Audio & Sound',
        ],
        [
            'pid' => 'CJ-4K-MINI-DRONE-04',
            'productNameEn' => 'AtoZ 4K Ultra HD Foldable Mini Drone with Obstacle Avoidance',
            'productSku' => 'CJ-DRONE-4K',
            'sellPrice' => 42.00,
            'productImage' => 'https://images.unsplash.com/photo-1527977966376-1c8408f9f108?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Drones & Toys',
        ],
        [
            'pid' => 'CJ-SMART-BOTTLE-05',
            'productNameEn' => 'AtoZ Digital Temperature Display Smart Vacuum Flask 500ml',
            'productSku' => 'CJ-BOTTLE-LED',
            'sellPrice' => 8.90,
            'productImage' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Home & Kitchen',
        ],
        [
            'pid' => 'CJ-PORTABLE-BLENDER-06',
            'productNameEn' => 'AtoZ USB Rechargeable Personal Smoothie Juicer Blender 6 Blades',
            'productSku' => 'CJ-BLENDER-USB',
            'sellPrice' => 11.50,
            'productImage' => 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Home & Kitchen',
        ]
    ];

    public function import()
    {
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();
        $cjCategories = \App\Services\Cj\CjProductService::getCategories();
        $brands = \App\Models\Brand::all();
        $stagedProducts = \App\Models\Product::where('fulfillment_type', 'cj')->get();
        return view('admin.catalog.import', compact('categories', 'cjCategories', 'brands', 'stagedProducts'));
    }

    public function getCjCategories()
    {
        return response()->json([
            'success' => true,
            'data' => \App\Services\Cj\CjProductService::getCategories()
        ]);
    }

    private function getCjAccessToken()
    {
        return \App\Services\Cj\CjAuthService::getAccessToken();
    }

    public function searchCjApi(Request $request)
    {
        $keyword = trim($request->query('keyword', ''));
        $filters = [];
        
        if ($request->filled('categoryId')) {
            $filters['categoryId'] = $request->query('categoryId');
        }
        if ($request->filled('countryCode')) {
            $filters['countryCode'] = $request->query('countryCode');
        }
        if ($request->filled('minPrice')) {
            $filters['minPrice'] = $request->query('minPrice');
        }
        if ($request->filled('maxPrice')) {
            $filters['maxPrice'] = $request->query('maxPrice');
        }

        $result = \App\Services\Cj\CjProductService::searchProducts($keyword, 1, 100, $filters);
        
        return response()->json([
            'result' => true,
            'message' => 'Success',
            'data' => [
                'list' => $result['list'] ?? [],
                'total' => $result['total'] ?? 0,
            ]
        ]);
    }


    public function importCjProduct(Request $request)
    {
        try {
            $data = $request->validate([
                'pid' => 'required|string',
                'title' => 'required|string',
                'price' => 'required|numeric|min:0.01',
                'image' => 'required|string',
                'category' => 'nullable|string',
                'categoryId' => 'nullable|integer',
                'markup' => 'nullable|numeric|min:1.0|max:10.0',
                'publish_now' => 'nullable|boolean',
            ]);

            // Idempotency guard: prevent duplicate imports and orphaned products
            $existing = \App\Models\CjProduct::where('cj_product_id', $data['pid'])->first();
            if ($existing && $existing->internal_product_id) {
                return response()->json([
                    'success' => true,
                    'internal_id' => $existing->internal_product_id,
                    'status' => 'already_imported',
                    'message' => 'Product is already staged in your catalog.'
                ]);
            }

            $category = (!empty($data['categoryId']) ? \App\Models\Category::find($data['categoryId']) : null)
                ?: (\App\Models\Category::first() ?: \App\Models\Category::create(['name' => $data['category'] ?? 'General', 'slug' => 'cat-' . uniqid(), 'status' => 'active']));
            $categoryId = $category->id;

            $userId = auth('sanctum')->id() ?: auth()->id();
            $createdBy = ($userId && \App\Models\User::where('id', $userId)->exists())
                ? $userId
                : (\App\Models\User::value('id') ?: null);

            // Compute dynamic tiered pricing
            $customMultiplier = !empty($data['markup']) ? (float)$data['markup'] : null;
            $pricing = \App\Services\Catalog\PricingService::calculateRetailPrice((float)$data['price'], $customMultiplier);

            // Clean title logic
            $cleanTitle = $data['title'];
            if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $cleanTitle) || strlen(trim($cleanTitle)) < 3) {
                $cleanTitle = 'AtoZ Smart Gadget - Imported Edition';
            }
            $cleanTitle = substr(trim($cleanTitle), 0, 200);
            $slug = Str::slug($cleanTitle) . '-' . substr((string) Str::uuid(), 0, 6);

            $status = !empty($data['publish_now']) ? 'active' : 'draft';
            $isActive = ($status === 'active');

            // Fetch live/mock details and variants for this PID
            $cjDetails = \App\Services\Cj\CjProductService::getProductDetails($data['pid']);

            // Strict ACID Transaction - Create Product, Variants, Media, Specs & CJ Supplier Mapping
            $product = \Illuminate\Support\Facades\DB::transaction(function () use ($categoryId, $createdBy, $cleanTitle, $slug, $data, $pricing, $status, $isActive, $cjDetails, $customMultiplier) {
                $cleanDescription = !empty($cjDetails['description']) 
                    ? strip_tags($cjDetails['description'], '<p><br><ul><li><strong><b><em>') 
                    : "{$cleanTitle} is crafted for high performance and dependable durability. Sourced and inspected to meet premium consumer standards.";

                $product = Product::create([
                    'category_id' => $categoryId,
                    'name' => $cleanTitle,
                    'slug' => $slug,
                    'sku' => $cjDetails['sku'] ?? ('CJ-' . substr((string) Str::uuid(), 0, 8)),
                    'price' => $pricing['selling_price'],
                    'discount_price' => round($pricing['selling_price'] * 0.85, 2),
                    'description' => $cleanDescription,
                    'thumbnail_image' => $data['image'],
                    'stock_quantity' => 100,
                    'status' => $status,
                    'is_active' => $isActive,
                    'fulfillment_type' => 'cj',
                    'created_by' => $createdBy
                ]);

                // 1. Create Supplier CJ Product Record
                CjProduct::updateOrCreate(
                    ['cj_product_id' => $data['pid']],
                    [
                        'internal_product_id' => $product->id,
                        'title' => $cleanTitle,
                        'sell_price' => $data['price'],
                        'cj_image' => $data['image'],
                        'category_name' => $data['category'] ?? 'General',
                        'status' => 'imported'
                    ]
                );

                // 2. Populate Media Gallery (Shields raw external CDN URLs)
                \App\Models\ProductMedia::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'url' => $data['image'],
                    'alt_text' => $cleanTitle,
                    'sort_order' => 0,
                    'is_primary' => true,
                ]);

                if (!empty($cjDetails['images']) && is_array($cjDetails['images'])) {
                    foreach (array_slice($cjDetails['images'], 0, 6) as $idx => $imgUrl) {
                        if ($imgUrl !== $data['image']) {
                            \App\Models\ProductMedia::create([
                                'product_id' => $product->id,
                                'type' => 'image',
                                'url' => $imgUrl,
                                'alt_text' => "{$cleanTitle} - View " . ($idx + 2),
                                'sort_order' => $idx + 1,
                                'is_primary' => false,
                            ]);
                        }
                    }
                }

                // 3. Populate Product Specifications
                if (!empty($cjDetails['attributes']) && is_array($cjDetails['attributes'])) {
                    foreach ($cjDetails['attributes'] as $idx => $attr) {
                        if (!empty($attr['name']) && !empty($attr['value'])) {
                            \App\Models\ProductSpecification::create([
                                'product_id' => $product->id,
                                'name' => trim($attr['name']),
                                'value' => trim($attr['value']),
                                'sort_order' => $idx,
                            ]);
                        }
                    }
                }

                // 4. Create Supplier Variants & Commercial Storefront Variants (PID vs VID split)
                if (!empty($cjDetails['variants'])) {
                    foreach ($cjDetails['variants'] as $v) {
                        $vPricing = \App\Services\Catalog\PricingService::calculateRetailPrice((float)$v['costPrice'], $customMultiplier);

                        // Supplier variant (VID)
                        \App\Models\CjVariant::updateOrCreate(
                            ['cj_variant_id' => $v['vid']],
                            [
                                'cj_product_id' => $data['pid'],
                                'cj_variant_sku' => $v['variantSku'] ?? '',
                                'variant_name' => $v['variantName'] ?? 'Standard Variant',
                                'option1_name' => $v['option1_name'] ?? null,
                                'option1_value' => $v['option1_value'] ?? null,
                                'option2_name' => $v['option2_name'] ?? null,
                                'option2_value' => $v['option2_value'] ?? null,
                                'cost_price' => $v['costPrice'],
                                'inventory_quantity' => $v['inventory'] ?? 100,
                                'status' => 'available',
                                'raw_data' => $v,
                            ]
                        );

                        // Commercial product variant
                        $variant = \App\Models\ProductVariant::create([
                            'product_id' => $product->id,
                            'cj_variant_id' => $v['vid'],
                            'sku' => $v['variantSku'] ?? ($product->sku . '-' . substr(md5($v['vid']), 0, 4)),
                            'name' => $v['variantName'] ?? 'Standard Variant',
                            'option1_name' => $v['option1_name'] ?? null,
                            'option1_value' => $v['option1_value'] ?? null,
                            'option2_name' => $v['option2_name'] ?? null,
                            'option2_value' => $v['option2_value'] ?? null,
                            'selling_price' => $vPricing['selling_price'],
                            'cost_price' => $v['costPrice'],
                            'stock_quantity' => $v['inventory'] ?? 100,
                            'status' => 'active',
                            'image_url' => $v['image'] ?? $data['image'],
                        ]);

                        if (!empty($v['image']) && $v['image'] !== $data['image']) {
                            \App\Models\ProductMedia::create([
                                'product_id' => $product->id,
                                'variant_id' => $variant->id,
                                'type' => 'image',
                                'url' => $v['image'],
                                'alt_text' => "{$cleanTitle} - " . ($v['variantName'] ?? 'Variant'),
                                'sort_order' => 99,
                                'is_primary' => false,
                            ]);
                        }
                    }
                }

                return $product;
            });

            return response()->json([
                'success' => true,
                'internal_id' => $product->id,
                'pricing' => $pricing,
                'status' => $status,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'message' => $ve->getMessage(), 'errors' => $ve->errors()], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('CJ Import Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleProductStatus($id)
    {
        $product = Product::findOrFail($id);
        $newStatus = ($product->status === 'active') ? 'draft' : 'active';
        $product->update([
            'status' => $newStatus,
            'is_active' => ($newStatus === 'active'),
            'stock_quantity' => ($product->stock_quantity <= 0 && $newStatus === 'active') ? 100 : $product->stock_quantity
        ]);

        return back()->with('success', "Product '{$product->name}' is now " . ($newStatus === 'active' ? 'Live on Storefront 🟢' : 'Draft / Staged 🟡'));
    }
}
