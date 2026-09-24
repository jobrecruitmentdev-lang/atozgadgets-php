<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations to batch sync all catalog products to fixed profit ($2.00) rule.
     */
    public function up()
    {
        try {
            $fixedProfit = 2.00;

            // 1. Sync all products linked with cj_products
            $cjProducts = DB::table('cj_products')->get();

            foreach ($cjProducts as $cj) {
                $targetProductId = $cj->internal_product_id;

                // If internal_product_id is null, attempt to match by SKU
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

                $cost = (float)($cj->original_price ?? 0);
                if ($cost <= 0) {
                    // Try checking variants cost
                    $varCost = DB::table('product_variants')
                        ->where('product_id', $targetProductId)
                        ->where('cost_price', '>', 0)
                        ->min('cost_price');
                    if ($varCost && (float)$varCost > 0) {
                        $cost = (float)$varCost;
                        DB::table('cj_products')->where('id', $cj->id)->update(['original_price' => $cost]);
                    }
                }

                if ($cost > 0) {
                    $newSellingPrice = round($cost + $fixedProfit, 2);

                    DB::table('products')
                        ->where('id', $targetProductId)
                        ->update([
                            'price' => $newSellingPrice,
                            'discount_price' => null,
                        ]);

                    DB::table('cj_products')
                        ->where('id', $cj->id)
                        ->update([
                            'sell_price' => $newSellingPrice,
                        ]);
                }
            }

            // 2. Sync all product variants
            $variants = DB::table('product_variants')->get();
            foreach ($variants as $variant) {
                $cost = (float)($variant->cost_price ?? 0);
                if ($cost > 0) {
                    $newSellingPrice = round($cost + $fixedProfit, 2);
                    DB::table('product_variants')
                        ->where('id', $variant->id)
                        ->update([
                            'selling_price' => $newSellingPrice,
                        ]);
                }
            }

            // 3. Confirm System Settings
            $settings = [
                'pricing_mode' => 'fixed_profit',
                'fixed_profit_amount' => '2.00',
            ];

            foreach ($settings as $key => $val) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    [
                        'value' => $val,
                        'group' => 'shipping',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            // 4. Invalidate caches
            Cache::flush();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Batch fixed profit migration notice: ' . $e->getMessage());
        }
    }

    public function down()
    {
        // Non-destructive rollback
    }
};
