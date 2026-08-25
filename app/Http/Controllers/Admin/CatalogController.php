<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\CjProduct;
use App\Services\Catalog\ProductContentService;
use App\Services\Catalog\PricingService;
use App\Services\Cj\CjProductService;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function import()
    {
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();
        $cjCategories = CjProductService::getCategories();
        $brands = \App\Models\Brand::all();
        $stagedProducts = \App\Models\Product::where('fulfillment_type', 'cj')->get();
        return view('admin.catalog.import', compact('categories', 'cjCategories', 'brands', 'stagedProducts'));
    }

    public function getCjCategories()
    {
        $categories = \Illuminate\Support\Facades\Cache::remember('cj_categories_list', 3600, function () {
            return CjProductService::getCategories();
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
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

        $cacheKey = 'cj_search_' . md5($keyword . '_' . json_encode($filters));
        $result = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($keyword, $filters) {
            return CjProductService::searchProducts($keyword, 1, 100, $filters);
        });
        
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
            $categoryName = $category->name;

            // Deterministic Brand Resolution: Map to existing brand or assign default authoritative brand entity
            $brandId = null;
            if (!empty($data['brandId'])) {
                $brandId = \App\Models\Brand::where('id', $data['brandId'])->value('id');
            }
            if (!$brandId) {
                $defaultBrand = \App\Models\Brand::firstOrCreate(
                    ['name' => 'AtoZGadgets'],
                    ['slug' => 'atozgadgets', 'status' => 'active']
                );
                $brandId = $defaultBrand->id;
            }

            $userId = auth('sanctum')->id() ?: auth()->id();
            $createdBy = ($userId && \App\Models\User::where('id', $userId)->exists())
                ? $userId
                : (\App\Models\User::value('id') ?: null);

            // Compute dynamic tiered pricing
            $customMultiplier = !empty($data['markup']) ? (float)$data['markup'] : null;
            $pricing = PricingService::calculateRetailPrice((float)$data['price'], $customMultiplier);

            // 1. Pillar 2: Content Normalization Layer
            $cleanTitle = ProductContentService::normalizeTitle($data['title'], $categoryName);
            $slug = Str::slug($cleanTitle) . '-' . substr((string) Str::uuid(), 0, 6);

            // Fetch live/mock details and variants for this PID
            $cjDetails = CjProductService::getProductDetails($data['pid']);
            $rawDesc = $cjDetails['description'] ?? $data['title'];
            $cleanDescription = ProductContentService::normalizeDescription($rawDesc, $cleanTitle, $categoryName);

            // Generate clean customer-safe Merchant SKU (Pillar 6)
            $merchantSku = ProductContentService::generateMerchantSku($categoryName);

            // 2. Pillar 8: Product Import Validation Gate
            $validation = ProductContentService::validateForPublish([
                'name' => $cleanTitle,
                'description' => $cleanDescription,
                'category_id' => $categoryId,
                'thumbnail_image' => $data['image'],
                'price' => $pricing['selling_price'],
                'sku' => $merchantSku,
            ]);

            $targetPublish = !empty($data['publish_now']);
            $status = ($targetPublish && $validation['can_publish']) ? 'active' : 'draft';
            $isActive = ($status === 'active');

            // Eagerly download media to local disk to avoid runtime proxy latency & cache stampedes
            $localThumbnail = ProductContentService::downloadAndStoreMedia($data['image']);

            // Strict ACID Transaction - Create Product, Variants, Media, Specs & CJ Supplier Mapping
            $product = \Illuminate\Support\Facades\DB::transaction(function () use ($categoryId, $brandId, $createdBy, $cleanTitle, $slug, $merchantSku, $data, $pricing, $status, $isActive, $cleanDescription, $cjDetails, $customMultiplier, $localThumbnail) {
                $product = Product::create([
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'name' => $cleanTitle,
                    'slug' => $slug,
                    'sku' => $merchantSku,
                    'price' => $pricing['selling_price'],
                    'discount_price' => round($pricing['selling_price'] * 0.85, 2),
                    'description' => $cleanDescription,
                    'thumbnail_image' => $localThumbnail,
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

                // 2. Populate Media Gallery (Pillar 1)
                \App\Models\ProductMedia::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'url' => $localThumbnail,
                    'storage_path' => str_starts_with($localThumbnail, '/storage/') ? ltrim(str_replace('/storage/', '', $localThumbnail), '/') : null,
                    'alt_text' => $cleanTitle,
                    'sort_order' => 0,
                    'is_primary' => true,
                ]);

                if (!empty($cjDetails['images']) && is_array($cjDetails['images'])) {
                    foreach (array_slice($cjDetails['images'], 0, 6) as $idx => $imgUrl) {
                        if ($imgUrl !== $data['image']) {
                            $localGalleryImg = ProductContentService::downloadAndStoreMedia($imgUrl);
                            \App\Models\ProductMedia::create([
                                'product_id' => $product->id,
                                'type' => 'image',
                                'url' => $localGalleryImg,
                                'storage_path' => str_starts_with($localGalleryImg, '/storage/') ? ltrim(str_replace('/storage/', '', $localGalleryImg), '/') : null,
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

                // 4. Create Supplier Variants & Commercial Storefront Variants
                if (!empty($cjDetails['variants'])) {
                    foreach ($cjDetails['variants'] as $vIdx => $v) {
                        $vPricing = PricingService::calculateRetailPrice((float)$v['costPrice'], $customMultiplier);
                        $vSku = $merchantSku . '-V' . str_pad((string)($vIdx + 1), 2, '0', STR_PAD_LEFT);

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
                            'sku' => $vSku,
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
                'merchant_sku' => $product->sku,
                'pricing' => $pricing,
                'status' => $status,
                'validation' => $validation,
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