<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('cj_variant_id')->nullable()->index();
                $table->string('sku')->nullable()->index();
                $table->string('name');
                $table->string('option1_name')->nullable();
                $table->string('option1_value')->nullable();
                $table->string('option2_name')->nullable();
                $table->string('option2_value')->nullable();
                $table->decimal('selling_price', 10, 2);
                $table->decimal('cost_price', 10, 2)->default(0.00);
                $table->integer('stock_quantity')->default(0);
                $table->string('status')->default('active');
                $table->text('image_url')->nullable();
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('cj_variants')) {
            Schema::create('cj_variants', function (Blueprint $table) {
                $table->id();
                $table->string('cj_product_id')->index();
                $table->string('cj_variant_id')->unique();
                $table->string('cj_variant_sku')->nullable()->index();
                $table->string('variant_name');
                $table->string('option1_name')->nullable();
                $table->string('option1_value')->nullable();
                $table->string('option2_name')->nullable();
                $table->string('option2_value')->nullable();
                $table->decimal('cost_price', 10, 2)->default(0.00);
                $table->integer('inventory_quantity')->default(0);
                $table->string('status')->default('available');
                $table->json('raw_data')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('cj_variants');
    }
};
