<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Services\Seo\SeoMetadataService;

return new class extends Migration
{
    /**
     * Run the migrations to enrich all catalog products with SEO, AEO direct answers, and technical specifications.
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('products')) {
                return;
            }

            $products = Product::with(['category', 'specifications'])->get();

            foreach ($products as $product) {
                $seoDesc = SeoMetadataService::getMetaDescription($product);
                $aeoDirect = SeoMetadataService::getAeoDirectAnswer($product);

                // 1. Update short_description if empty
                $currentShort = trim(strip_tags((string)($product->short_description ?? '')));
                if (empty($currentShort)) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['short_description' => $seoDesc]);
                }

                // 2. Update description if empty or raw empty string
                $currentDesc = trim(strip_tags((string)($product->description ?? '')));
                if (empty($currentDesc) || strlen($currentDesc) < 15) {
                    $richDesc = "<p>{$aeoDirect}</p><p>Every order curated by <strong>AtoZGadgets</strong> is rigorously inspected for premium craftsmanship and packaged with factory-sealed accessories. Includes our 30-day money-back guarantee and live USPS/UPS courier tracking across the USA.</p>";
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['description' => $richDesc]);
                }

                // 3. Populate product_specifications table if empty for this product
                if (Schema::hasTable('product_specifications')) {
                    $existingSpecsCount = DB::table('product_specifications')
                        ->where('product_id', $product->id)
                        ->count();

                    if ($existingSpecsCount === 0) {
                        $dynamicSpecs = SeoMetadataService::getDynamicSpecifications($product);
                        $sort = 0;
                        foreach ($dynamicSpecs as $name => $val) {
                            DB::table('product_specifications')->insert([
                                'product_id' => $product->id,
                                'name' => $name,
                                'value' => (string)$val,
                                'sort_order' => $sort++,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // Invalidate caches
            Cache::flush();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Migration enrich_catalog_seo warning: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Non-destructive data enrichment, no destructive rollback needed
    }
};
