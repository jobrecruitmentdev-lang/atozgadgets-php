<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations to resync legacy products and variants to CJ Cost + $2.00 Fixed Profit.
     */
    public function up()
    {
        try {
            $fixedProfit = 2.00;

            // 1. Process all products in cj_products
            $cjProducts = DB::table('cj_products')->get();

            foreach ($cjProducts as $cj) {
                $targetProductId = $cj->internal_product_id;

                if (!$targetProductId && !empty($cj->sku)) {
                    $matched = DB::table('products')->where('sku', $cj->sku)->first();
                    if ($matched) {
                        $targetProductId = $matched->id;
                        DB::table('cj_products')->where('id', $cj->id)->update(['internal_product_id' => $matched->id]);
                    }
                }

                if (!$targetProductId) {
                    continue;
                }

                $prod = DB::table('products')->where('id', $targetProductId)->first();
                if (!$prod) {
                    continue;
                }

                // Determine the true CJ Wholesale Cost:
                $cost = 0.0;

                // Priority A: cj_products.original_price
                if (!empty($cj->original_price) && (float)$cj->original_price > 0) {
                    $cost = (float)$cj->original_price;
                }

                // Priority B: cj_variants.cost_price
                if ($cost <= 0 && !empty($cj->cj_product_id)) {
                    $vCost = DB::table('cj_variants')
                        ->where('cj_product_id', $cj->cj_product_id)
                        ->where('cost_price', '>', 0)
                        ->min('cost_price');
                    if ($vCost && (float)$vCost > 0) {
                        $cost = (float)$vCost;
                    }
                }

                // Priority C: product_variants.cost_price
                if ($cost <= 0) {
                    $pvCost = DB::table('product_variants')
                        ->where('product_id', $targetProductId)
                        ->where('cost_price', '>', 0)
                        ->min('cost_price');
                    if ($pvCost && (float)$pvCost > 0) {
                        $cost = (float)$pvCost;
                    }
                }

                // Priority D: legacy cj_products.sell_price (which held the raw $data['price'] from CJ API)
                if ($cost <= 0 && !empty($cj->sell_price) && (float)$cj->sell_price > 0 && (float)$cj->sell_price < (float)$prod->price) {
                    $cost = (float)$cj->sell_price;
                }

                if ($cost > 0) {
                    $newPrice = round($cost + $fixedProfit, 2);

                    DB::table('products')->where('id', $targetProductId)->update([
                        'price' => $newPrice,
                        'discount_price' => null,
                    ]);

                    DB::table('cj_products')->where('id', $cj->id)->update([
                        'original_price' => $cost,
                        'sell_price' => $newPrice,
                    ]);
                }
            }

            // 2. Also update all variants to cost_price + fixedProfit
            $variants = DB::table('product_variants')->get();
            foreach ($variants as $variant) {
                $cost = (float)($variant->cost_price ?? 0);
                if ($cost <= 0 && !empty($variant->cj_variant_id)) {
                    $cjVar = DB::table('cj_variants')->where('cj_variant_id', $variant->cj_variant_id)->first();
                    if ($cjVar && (float)$cjVar->cost_price > 0) {
                        $cost = (float)$cjVar->cost_price;
                        DB::table('product_variants')->where('id', $variant->id)->update(['cost_price' => $cost]);
                    }
                }

                if ($cost > 0) {
                    DB::table('product_variants')->where('id', $variant->id)->update([
                        'selling_price' => round($cost + $fixedProfit, 2),
                    ]);
                }
            }

            Cache::flush();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Resync legacy CJ costs notice: ' . $e->getMessage());
        }
    }

    public function down()
    {
    }
};
