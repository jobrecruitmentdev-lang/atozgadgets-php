<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Services\Cj\CjProductService;

return new class extends Migration
{
    /**
     * Run the migrations to repair all existing product images.
     */
    public function up()
    {
        try {
            // 1. Repair products thumbnail_image from cj_products table
            $cjProducts = DB::table('cj_products')
                ->whereNotNull('internal_product_id')
                ->whereNotNull('cj_image')
                ->where('cj_image', '!=', '')
                ->get();

            foreach ($cjProducts as $cj) {
                $cleanImg = CjProductService::normalizeImageUrl($cj->cj_image);
                if (empty($cleanImg)) {
                    continue;
                }

                // Update cj_products with normalized URL
                DB::table('cj_products')
                    ->where('id', $cj->id)
                    ->update(['cj_image' => $cleanImg]);

                // Update products table if thumbnail is empty or dead local storage path
                $prod = DB::table('products')->where('id', $cj->internal_product_id)->first();
                if ($prod) {
                    $currThumb = $prod->thumbnail_image ?? '';
                    $needsUpdate = empty($currThumb) 
                        || str_starts_with($currThumb, '//') 
                        || (str_starts_with($currThumb, '/storage/') && !file_exists(public_path(ltrim($currThumb, '/'))));

                    if ($needsUpdate) {
                        DB::table('products')
                            ->where('id', $prod->id)
                            ->update(['thumbnail_image' => $cleanImg]);
                    }

                    // Update primary product_media if empty
                    $media = DB::table('product_media')
                        ->where('product_id', $prod->id)
                        ->where('is_primary', true)
                        ->first();

                    if (!$media || empty($media->url) || (str_starts_with($media->url, '/storage/') && !file_exists(public_path(ltrim($media->url, '/'))))) {
                        if ($media) {
                            DB::table('product_media')
                                ->where('id', $media->id)
                                ->update(['url' => $cleanImg]);
                        } else {
                            DB::table('product_media')->insert([
                                'product_id' => $prod->id,
                                'type' => 'image',
                                'url' => $cleanImg,
                                'alt_text' => $prod->name ?? 'Product Image',
                                'sort_order' => 0,
                                'is_primary' => true,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // 2. Normalize all remaining products thumbnail_image starting with //
            $rawProds = DB::table('products')
                ->where('thumbnail_image', 'LIKE', '//%')
                ->get();

            foreach ($rawProds as $rp) {
                $clean = CjProductService::normalizeImageUrl($rp->thumbnail_image);
                if (!empty($clean)) {
                    DB::table('products')
                        ->where('id', $rp->id)
                        ->update(['thumbnail_image' => $clean]);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Migration repair_cj_product_images warning: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // No-op (data correction is non-destructive)
    }
};
