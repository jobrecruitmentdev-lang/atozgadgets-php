<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('subcategory_id')->references('id')->on('sub_categories')->onDelete('restrict');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->unique()->nullable();
            
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0.00)->nullable();
            
            $table->integer('stock_quantity')->default(0);
            $table->float('weight')->nullable();
            $table->float('length')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->string('thumbnail_image')->nullable();
            
            $table->string('handle')->nullable();
            $table->string('title')->nullable();
            $table->string('option1_name', 100)->nullable();
            $table->string('option2_name', 100)->nullable();
            $table->string('option3_name', 100)->nullable();
            $table->string('hs_code', 100)->nullable();
            $table->string('country_of_origin', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('bin_name', 100)->nullable();
            
            $table->integer('incoming')->default(0);
            $table->integer('unavailable')->default(0);
            $table->integer('committed')->default(0);
            $table->integer('available')->default(0);
            $table->integer('onhand_old')->default(0);
            $table->integer('onhand_new')->default(0);

            $table->string('status', 50)->default('active')->nullable();
            $table->boolean('is_featured')->default(false)->nullable();
            $table->boolean('is_active')->default(true)->nullable();
            
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->string('fulfillment_type')->default('cj');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
