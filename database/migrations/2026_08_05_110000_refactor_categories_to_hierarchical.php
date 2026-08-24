<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RefactorCategoriesToHierarchical extends Migration
{
    public function up()
    {
        // 1. Add parent_id to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->onDelete('cascade');
        });

        // 2. Migrate data from sub_categories to categories
        if (Schema::hasTable('sub_categories')) {
            $subcategories = DB::table('sub_categories')->get();
            $subCatIdMapping = []; // Old SubCategory ID => New Category ID
            
            foreach ($subcategories as $sub) {
                // Ensure slug is unique
                $slug = $sub->slug;
                $count = 1;
                while (DB::table('categories')->where('slug', $slug)->exists()) {
                    $slug = $sub->slug . '-' . $count;
                    $count++;
                }

                $newId = DB::table('categories')->insertGetId([
                    'parent_id' => $sub->category_id,
                    'name' => $sub->name,
                    'slug' => $slug,
                    'description' => $sub->description,
                    'status' => $sub->status,
                    'created_at' => $sub->created_at,
                    'updated_at' => $sub->updated_at,
                ]);
                $subCatIdMapping[$sub->id] = $newId;
            }

            // 3. Update products: Move subcategory_id data to category_id
            if (Schema::hasTable('products')) {
                $products = DB::table('products')->whereNotNull('subcategory_id')->get();
                foreach ($products as $product) {
                    if (isset($subCatIdMapping[$product->subcategory_id])) {
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update(['category_id' => $subCatIdMapping[$product->subcategory_id]]);
                    }
                }

                // Drop foreign key and column from products
                if (DB::getDriverName() !== 'sqlite') {
                    Schema::table('products', function (Blueprint $table) {
                        $table->dropForeign(['subcategory_id']);
                        $table->dropColumn('subcategory_id');
                    });
                }
            }

            // 4. Drop sub_categories table
            Schema::dropIfExists('sub_categories');
        }
    }

    public function down()
    {
        // Since this is a destructive one-way data migration, down is complex to implement fully accurately.
        // We will just recreate the schema for rollback safety.
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('subcategory_id')->nullable()->references('id')->on('sub_categories')->onDelete('restrict');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
}
