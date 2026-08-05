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
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'tech-test-slug'],
            ['name' => 'Tech Test', 'status' => 'active']
        );
        $product = \App\Models\Product::firstOrCreate(
            ['slug' => 'smart-watch-test'],
            [
                'name' => 'Smart Watch',
                'sku' => 'SKU-SMART-WATCH-TEST',
                'price' => 199.99,
                'category_id' => $category->id,
                'subcategory_id' => 1,
                'created_by' => 1,
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
