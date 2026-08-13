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
}
