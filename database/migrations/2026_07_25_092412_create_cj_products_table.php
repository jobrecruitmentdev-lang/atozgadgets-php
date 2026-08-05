<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCjProductsTable extends Migration
{
    public function up()
    {
        Schema::create('cj_products', function (Blueprint $table) {
            $table->id();
            $table->string('cj_product_id', 100)->unique();
            $table->foreignId('internal_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('title', 255)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('category_name', 255)->nullable();
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('sell_price', 10, 2)->nullable();
            $table->float('weight')->nullable();
            $table->string('cj_image')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('list_status', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cj_products');
    }
}
