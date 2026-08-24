<?php

namespace App\Services\Cj;

use App\Models\Category;
use App\Models\Product;
use App\Models\CjProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CjSyncService
{
    /**
     * Processes a single page of CJ API results using O(1) Bulk Upserts.
     * This eliminates the N+1 query problem by doing exactly 3 queries per 20 items.
     */
    public static function processCategoryPage($category, $subCategoryIdOrPage = 1, $page = 1)
    {
        $targetPage = func_num_args() >= 3 ? $page : (is_numeric($subCategoryIdOrPage) ? $subCategoryIdOrPage : 1);
        $targetCategoryId = (func_num_args() >= 3 && is_numeric($subCategoryIdOrPage)) ? $subCategoryIdOrPage : $category->id;

        try {
            $result = CjProductService::searchProducts($category->cj_keyword, $targetPage, 20);
            $list = $result['list'] ?? [];

            if (empty($list)) {
                return false; // No more items
            }

            $productUpserts = [];
            $skus = [];

            // 1. Prepare Product Upsert Payload
            foreach ($list as $item) {
                $pid = $item['id'] ?? ($item['pid'] ?? null);
                if (!$pid) continue;

                $supplierPrice = (float)($item['sellPrice'] ?? ($item['nowPrice'] ?? ($item['price'] ?? '0')));
                $markupPercentage = 2.0; 
                $finalPrice = $supplierPrice * $markupPercentage;
                
                $name = substr((string)($item['nameEn'] ?? ($item['productNameEn'] ?? ($item['name'] ?? ''))), 0, 200);
                $imageUrl = $item['bigImage'] ?? ($item['imageUrl'] ?? ($item['productImage'] ?? ''));
                $sku = (string)$pid;

                $productUpserts[] = [
                    'category_id' => $targetCategoryId,
                    'name' => $name,
                    'slug' => Str::slug(substr($name, 0, 40)) . '-' . substr(md5($sku), 0, 6),
                    'sku' => $sku,
                    'price' => $finalPrice,
                    'stock_quantity' => 100, // Default stock
                    'weight' => 0,
                    'fulfillment_type' => 'cj',
                    'created_by' => 1,
                    'thumbnail_image' => $imageUrl,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $skus[] = $sku;
            }

            if (empty($productUpserts)) return true;

            // 2. O(1) Bulk Upsert Products (Requires 'sku' to be unique in schema)
            Product::upsert(
                $productUpserts,
                ['sku'], // Unique keys
                ['price', 'name', 'thumbnail_image', 'updated_at'] // Columns to update if exists
            );

            // 3. Fetch newly inserted/updated IDs in O(1)
            $dbProducts = Product::whereIn('sku', $skus)->pluck('id', 'sku');

            $cjProductUpserts = [];
            foreach ($list as $item) {
                $pid = (string)($item['id'] ?? ($item['pid'] ?? ''));
                if (!$pid || !isset($dbProducts[$pid])) continue;

                $supplierPrice = (float)($item['sellPrice'] ?? ($item['nowPrice'] ?? ($item['price'] ?? '0')));

                $cjProductUpserts[] = [
                    'cj_product_id' => $pid,
                    'sell_price' => $supplierPrice,
                    'internal_product_id' => $dbProducts[$pid],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 4. O(1) Bulk Upsert CJ Metadata
            if (!empty($cjProductUpserts)) {
                CjProduct::upsert(
                    $cjProductUpserts,
                    ['cj_product_id'],
                    ['sell_price', 'internal_product_id', 'updated_at']
                );
            }

            Log::info("[CjSyncService] Successfully bulk upserted " . count($productUpserts) . " products for category {$category->name} (Page {$page})");
            return true;

        } catch (\Exception $e) {
            Log::error("[CjSyncService] Failed to sync page {$page} for category {$category->name}: " . $e->getMessage());
            return false;
        }
    }
}
