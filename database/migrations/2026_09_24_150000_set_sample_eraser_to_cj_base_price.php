<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations to set the sample eraser product and its variants to the exact CJ base price ($0.09).
     */
    public function up()
    {
        try {
            // Find the eraser product by name or PID
            $products = DB::table('products')
                ->where('name', 'like', '%Eraser%')
                ->orWhere('name', 'like', '%Blackboard%')
                ->get();

            foreach ($products as $prod) {
                // Set product base price to 0.09 and clear discount
                DB::table('products')
                    ->where('id', $prod->id)
                    ->update([
                        'price' => 0.09,
                        'discount_price' => null,
                    ]);

                // Set variant prices to 0.09
                DB::table('product_variants')
                    ->where('product_id', $prod->id)
                    ->update([
                        'selling_price' => 0.09,
                        'cost_price' => 0.09,
                    ]);

                // Set cj_product price to 0.09
                DB::table('cj_products')
                    ->where('internal_product_id', $prod->id)
                    ->update([
                        'sell_price' => 0.09,
                    ]);
            }

            Cache::flush();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Migration set_sample_eraser_to_cj_base_price failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // No down migration needed for sample price adjustment
    }
};
