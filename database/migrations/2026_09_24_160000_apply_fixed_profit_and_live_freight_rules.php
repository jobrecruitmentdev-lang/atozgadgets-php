<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations to apply $2.00 fixed profit to sample products and configure live freight rules.
     */
    public function up()
    {
        try {
            // 1. Update Blackboard Eraser product & variants to $2.09 ($0.09 base cost + $2.00 profit)
            $products = DB::table('products')
                ->where('name', 'like', '%Eraser%')
                ->orWhere('name', 'like', '%Blackboard%')
                ->get();

            foreach ($products as $prod) {
                DB::table('products')
                    ->where('id', $prod->id)
                    ->update([
                        'price' => 2.09,
                        'discount_price' => null,
                    ]);

                DB::table('product_variants')
                    ->where('product_id', $prod->id)
                    ->update([
                        'selling_price' => 2.09,
                        'cost_price' => 0.09,
                    ]);

                DB::table('cj_products')
                    ->where('internal_product_id', $prod->id)
                    ->update([
                        'sell_price' => 2.09,
                    ]);
            }

            // 2. Set System Settings for Pricing & Shipping
            $settings = [
                'pricing_mode' => 'fixed_profit',
                'fixed_profit_amount' => '2.00',
                'free_shipping_threshold' => '99999.00',
                'standard_shipping_rate' => '5.07',
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

            Cache::flush();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Migration apply_fixed_profit_and_live_freight_rules failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // No down migration required
    }
};
