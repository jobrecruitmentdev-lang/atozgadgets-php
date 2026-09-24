<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations to fix mismatched placeholder smartwatch images and names on non-watch product variants.
     */
    public function up()
    {
        try {
            // Find all product variants that have the demo smartwatch image or demo smartwatch names
            $variants = DB::table('product_variants')
                ->join('products', 'product_variants.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select(
                    'product_variants.id as variant_id',
                    'product_variants.cj_variant_id',
                    'product_variants.name as variant_name',
                    'product_variants.image_url as variant_image',
                    'products.id as product_id',
                    'products.name as product_name',
                    'products.thumbnail_image as product_image',
                    'categories.name as category_name'
                )
                ->where(function ($query) {
                    $query->where('product_variants.image_url', 'like', '%photo-1546868871%')
                          ->orWhere('product_variants.image_url', 'like', '%photo-1523275335%')
                          ->orWhere('product_variants.name', 'like', '%Midnight Black / Standard%')
                          ->orWhere('product_variants.name', 'like', '%Titanium Silver / Pro%');
                })
                ->get();

            foreach ($variants as $row) {
                $pNameLower = strtolower($row->product_name ?? '');
                $cNameLower = strtolower($row->category_name ?? '');
                $isWatch = str_contains($pNameLower, 'watch') || str_contains($cNameLower, 'watch');

                // If this product is NOT a watch, it shouldn't have watch image or watch variant names!
                if (!$isWatch) {
                    $updates = [];

                    // 1. Fix image
                    if (str_contains($row->variant_image ?? '', 'photo-1546868871') || str_contains($row->variant_image ?? '', 'photo-1523275335') || empty($row->variant_image)) {
                        $updates['image_url'] = !empty($row->product_image) ? $row->product_image : 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=800&auto=format&fit=crop';
                    }

                    // 2. Fix variant name
                    if (str_contains($row->variant_name, 'Midnight Black / Standard') || str_contains($row->variant_name, 'Titanium Silver / Pro')) {
                        $updates['name'] = 'Standard';
                        $updates['option1_name'] = 'Option';
                        $updates['option1_value'] = 'Standard';
                        $updates['option2_name'] = null;
                        $updates['option2_value'] = null;
                    }

                    if (!empty($updates)) {
                        DB::table('product_variants')->where('id', $row->variant_id)->update($updates);
                    }

                    // 3. Fix ProductMedia gallery entries pointing to watch photo for this variant
                    if (DB::getSchemaBuilder()->hasTable('product_media')) {
                        DB::table('product_media')
                            ->where('variant_id', $row->variant_id)
                            ->where(function ($q) {
                                $q->where('url', 'like', '%photo-1546868871%')
                                  ->orWhere('url', 'like', '%photo-1523275335%');
                            })
                            ->update([
                                'url' => !empty($row->product_image) ? $row->product_image : 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=800&auto=format&fit=crop',
                                'alt_text' => $row->product_name . ' - Standard'
                            ]);
                    }

                    // 4. Fix CjVariant record name if applicable
                    if (!empty($row->cj_variant_id) && DB::getSchemaBuilder()->hasTable('cj_variants')) {
                        DB::table('cj_variants')
                            ->where('cj_variant_id', $row->cj_variant_id)
                            ->where(function ($q) {
                                $q->where('variant_name', 'like', '%Midnight Black / Standard%')
                                  ->orWhere('variant_name', 'like', '%Titanium Silver / Pro%');
                            })
                            ->update([
                                'variant_name' => 'Standard',
                                'option1_name' => 'Option',
                                'option1_value' => 'Standard',
                                'option2_name' => null,
                                'option2_value' => null
                            ]);
                    }
                }
            }

            Cache::flush();
        } catch (\Throwable $e) {
            Log::warning('Fix mismatched variant images error: ' . $e->getMessage());
        }
    }

    public function down()
    {
    }
};
