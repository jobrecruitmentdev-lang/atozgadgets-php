<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontViewTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test storefront home page.
     *
     * @return void
     */
    public function test_home_page_loads()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Get all the trending gadgets');
    }

    /**
     * Test storefront shop page.
     *
     * @return void
     */
    public function test_shop_page_loads()
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertSee('All Products');
    }

    /**
     * Test single product slug & ID resolution.
     *
     * @return void
     */
    public function test_single_product_loads()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'mobile' => '1234567890_' . time(),
                'role_id' => 1,
                'password' => 'secret',
                'is_active' => true
            ]
        );
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'tech-test-slug'],
            ['name' => 'Tech Test', 'status' => 'active']
        );
        $subcategory = \App\Models\Category::firstOrCreate(
            ['slug' => 'tech-sub-test-slug'],
            ['parent_id' => $category->id, 'name' => 'Tech Sub Test', 'status' => 'active']
        );
        $product = \App\Models\Product::firstOrCreate(
            ['slug' => 'smart-watch-test'],
            [
                'name' => 'Smart Watch',
                'sku' => 'SKU-SMART-WATCH-TEST',
                'price' => 199.99,
                'category_id' => $subcategory->id,
                'created_by' => $user->id,
                'is_active' => true
            ]
        );

        // Test slug resolution
        $slugResponse = $this->get('/product/' . $product->slug);
        $slugResponse->assertStatus(200);
        $slugResponse->assertSee('Smart Watch');

        // Test numeric ID resolution
        $idResponse = $this->get('/product/' . $product->id);
        $idResponse->assertStatus(200);
        $idResponse->assertSee('Smart Watch');
    }

    /**
     * Test shop pagination and desktop navigation dropdown structure.
     *
     * @return void
     */
    public function test_shop_pagination_and_desktop_dropdown_menus()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'mobile' => '1234567890_' . time(),
                'role_id' => 1,
                'password' => 'secret',
                'is_active' => true
            ]
        );
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'electronics-test'],
            ['name' => 'Electronics', 'status' => 'active']
        );
        $childCategory = \App\Models\Category::firstOrCreate(
            ['slug' => 'gadgets-test'],
            ['parent_id' => $category->id, 'name' => 'Smart Gadgets', 'status' => 'active']
        );

        // Create 25 products to trigger pagination (12 per page)
        for ($i = 1; $i <= 25; $i++) {
            \App\Models\Product::create([
                'slug' => 'test-product-' . $i,
                'name' => 'Test Product ' . $i,
                'sku' => 'SKU-TEST-' . $i,
                'price' => 10.00 + $i,
                'category_id' => $childCategory->id,
                'created_by' => $user->id,
                'is_active' => true
            ]);
        }

        $response = $this->get('/shop');
        $response->assertStatus(200);
        
        // Verify desktop dropdown mega-menu exists in HTML
        $response->assertSee('mega-dropdown');
        $response->assertSee('mega-menu');
        $response->assertSee('Deals');
        $response->assertSee('Under $50');

        // Verify custom responsive pagination is rendered
        $response->assertSee('custom-pagination');
        $response->assertSee('mobile-page-indicator');
        $response->assertSee('desktop-page-numbers');
    }
}

