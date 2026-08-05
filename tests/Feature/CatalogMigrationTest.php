<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Product;

class CatalogMigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test catalog models can be created.
     *
     * @return void
     */
    public function test_catalog_models_can_be_created()
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => 1,
                'first_name' => 'Admin',
                'mobile' => '+199999999',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'electronics'],
            ['name' => 'Electronics', 'status' => 'active']
        );

        $this->assertDatabaseHas('categories', ['slug' => 'electronics']);

        $subCategory = SubCategory::firstOrCreate(
            ['slug' => 'mobile-phones'],
            [
                'category_id' => $category->id,
                'name' => 'Mobile Phones',
            ]
        );

        $this->assertDatabaseHas('sub_categories', ['slug' => 'mobile-phones']);

        $brand = Brand::firstOrCreate(
            ['slug' => 'apple'],
            ['name' => 'Apple']
        );

        $this->assertDatabaseHas('brands', ['slug' => 'apple']);

        $product = Product::firstOrCreate(
            ['sku' => 'IPH15P-256'],
            [
                'category_id' => $category->id,
                'subcategory_id' => $subCategory->id,
                'brand_id' => $brand->id,
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'price' => 999.99,
                'created_by' => $user->id,
            ]
        );

        $this->assertDatabaseHas('products', ['sku' => 'IPH15P-256', 'price' => '999.99']);
    }
}
