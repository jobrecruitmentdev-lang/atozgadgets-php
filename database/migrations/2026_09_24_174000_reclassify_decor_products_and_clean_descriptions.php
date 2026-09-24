<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations to reclassify decor & lighting items out of watches, and clean supplier boilerplate from descriptions.
     */
    public function up()
    {
        try {
            // 1. Ensure target parent & sub categories exist
            // Parent: Home & Kitchen
            $homeParent = DB::table('categories')->where('slug', 'home-kitchen')->first();
            if (!$homeParent) {
                $homeParentId = DB::table('categories')->insertGetId([
                    'name' => 'Home & Kitchen',
                    'slug' => 'home-kitchen',
                    'parent_id' => null,
                    'description' => 'Explore premium Home & Kitchen gadgets at AtoZGadgets.',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $homeParentId = $homeParent->id;
            }

            // Subcategory: Smart Lighting & Lamps
            $lightingCat = DB::table('categories')->where('slug', 'smart-lighting-lamps')->first();
            if (!$lightingCat) {
                $lightingCatId = DB::table('categories')->insertGetId([
                    'name' => 'Smart Lighting & Lamps',
                    'slug' => 'smart-lighting-lamps',
                    'parent_id' => $homeParentId,
                    'description' => 'Shop trending smart lighting, night lights, and ambient lamps online.',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $lightingCatId = $lightingCat->id;
            }

            // Parent: Lifestyle & Personal Care
            $lifestyleParent = DB::table('categories')->where('slug', 'lifestyle-personal-care')->first();
            if (!$lifestyleParent) {
                $lifestyleParentId = DB::table('categories')->insertGetId([
                    'name' => 'Lifestyle & Personal Care',
                    'slug' => 'lifestyle-personal-care',
                    'parent_id' => null,
                    'description' => 'Explore lifestyle gadgets and personal accessories.',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $lifestyleParentId = $lifestyleParent->id;
            }

            // Subcategory: Novelty & Creative Gifts
            $noveltyCat = DB::table('categories')->where('slug', 'novelty-creative-gifts')->first();
            if (!$noveltyCat) {
                $noveltyCatId = DB::table('categories')->insertGetId([
                    'name' => 'Novelty & Creative Gifts',
                    'slug' => 'novelty-creative-gifts',
                    'parent_id' => $lifestyleParentId,
                    'description' => 'Shop novelty gifts, creative decorations, and festive accessories.',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $noveltyCatId = $noveltyCat->id;
            }

            // 2. Identify all products wrongly classified in watch or wearable categories
            $watchCategoryIds = DB::table('categories')
                ->where(function($q) {
                    $q->where('name', 'like', '%watch%')
                      ->orWhere('name', 'like', '%wearable%')
                      ->orWhere('slug', 'like', '%watch%')
                      ->orWhere('slug', 'like', '%wearable%');
                })
                ->pluck('id')
                ->toArray();

            $decorKeywords = ['christmas', 'decor', 'decoration', 'lamp', 'light', 'night', 'tree', 'lantern', 'candle', 'curtain', 'garland', 'ornament', 'pendant', 'flower'];

            $products = DB::table('products')->get();

            foreach ($products as $prod) {
                $nameLower = strtolower($prod->name);
                $isDecor = false;
                foreach ($decorKeywords as $kw) {
                    if (str_contains($nameLower, $kw)) {
                        $isDecor = true;
                        break;
                    }
                }

                // If it is in a watch category, move it to proper category!
                if ($isDecor && in_array($prod->category_id, $watchCategoryIds)) {
                    $targetCatId = $noveltyCatId;
                    if (str_contains($nameLower, 'lamp') || str_contains($nameLower, 'light') || str_contains($nameLower, 'lantern')) {
                        $targetCatId = $lightingCatId;
                    }

                    DB::table('products')->where('id', $prod->id)->update([
                        'category_id' => $targetCatId,
                    ]);
                }

                // Clean raw supplier boilerplate (Packing list, Product Image, etc.) from description
                if (!empty($prod->description)) {
                    $cleaned = \App\Services\Catalog\ProductContentService::sanitizeSupplierBoilerplate($prod->description);
                    if ($cleaned !== $prod->description) {
                        DB::table('products')->where('id', $prod->id)->update([
                            'description' => $cleaned,
                        ]);
                    }
                }
            }

            Cache::flush();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Reclassify decor products error: ' . $e->getMessage());
        }
    }

    public function down()
    {
    }
};
