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

    /**
     * Test that shop page renders products without malformed or unclosed script tags.
     */
    public function test_shop_page_renders_products_and_no_unclosed_scripts()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['first_name' => 'Admin', 'last_name' => 'User', 'mobile' => '1234567890_' . time(), 'role_id' => 1, 'password' => 'secret', 'is_active' => true]
        );
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'gadgets-verified'],
            ['name' => 'Verified Gadgets', 'status' => 'active']
        );
        $product = \App\Models\Product::create([
            'slug' => 'awesome-drone-verified',
            'name' => 'Awesome Mini Drone 4K',
            'sku' => 'SKU-DRONE-4K',
            'price' => 45.00,
            'discount_price' => 35.00,
            'category_id' => $category->id,
            'created_by' => $user->id,
            'is_active' => true
        ]);

        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertSee('Awesome Mini Drone 4K');
        $response->assertSee('$35.00'); // Effective price
        $response->assertSee('$45.00'); // Old price strikethrough
        
        // Ensure no unclosed script tag exists inside shop HTML before main-content
        $content = $response->getContent();
        $this->assertFalse(str_contains($content, '</aside>' . "\n\n" . '    <script>' . "\n" . '        document.addEventListener'));
    }

    /**
     * Test price hub effective price math and chevron breadcrumb navigation.
     */
    public function test_price_hub_effective_price_and_breadcrumb()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['first_name' => 'Admin', 'last_name' => 'User', 'mobile' => '1234567890_' . time(), 'role_id' => 1, 'password' => 'secret', 'is_active' => true]
        );
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'budget-tech'],
            ['name' => 'Budget Tech', 'status' => 'active']
        );

        // Product A: Price $60, discount $18.99 -> effective price $18.99 (should show in Under $20 and Under $50)
        $productA = \App\Models\Product::create([
            'slug' => 'discounted-earbuds',
            'name' => 'Discounted Earbuds',
            'sku' => 'SKU-DISC-EAR',
            'price' => 60.00,
            'discount_price' => 18.99,
            'category_id' => $category->id,
            'created_by' => $user->id,
            'is_active' => true
        ]);

        // Product B: Price $49.00 -> effective price $49.00 (should show in Under $50, but NOT in Under $20)
        $productB = \App\Models\Product::create([
            'slug' => 'standard-smart-lamp',
            'name' => 'Standard Smart Lamp',
            'sku' => 'SKU-LAMP',
            'price' => 49.00,
            'category_id' => $category->id,
            'created_by' => $user->id,
            'is_active' => true
        ]);

        // Visit /gadgets-under-20
        $response20 = $this->get('/gadgets-under-20');
        $response20->assertStatus(200);
        $response20->assertSee('Discounted Earbuds');
        $response20->assertSee('$18.99');
        $response20->assertDontSee('Standard Smart Lamp');

        // Verify breadcrumb uses chevron-right and not plain slashes
        $content20 = $response20->getContent();
        $this->assertStringContainsString('breadcrumb-current', $content20);
        $this->assertStringNotContainsString('<span>/</span>', $content20);

        // Visit /gadgets-under-50
        $response50 = $this->get('/gadgets-under-50');
        $response50->assertStatus(200);
        $response50->assertSee('Discounted Earbuds');
        $response50->assertSee('Standard Smart Lamp');
    }

    /**
     * Test variant display name displays clean option values instead of duplicate product title.
     */
    public function test_variant_display_name_shows_clean_options()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['first_name' => 'Admin', 'last_name' => 'User', 'mobile' => '1234567890_' . time(), 'role_id' => 1, 'password' => 'secret', 'is_active' => true]
        );
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'apparel-test'],
            ['name' => 'Apparel Test', 'status' => 'active']
        );
        $product = \App\Models\Product::create([
            'slug' => 'slim-tube-top-long-dress-fashion',
            'name' => 'Slim Tube Top Long Dress Fashion Bandeau',
            'sku' => 'SKU-DRESS-1',
            'price' => 25.00,
            'category_id' => $category->id,
            'created_by' => $user->id,
            'is_active' => true
        ]);

        // Variant where name was set to the product title, but option1_value is Black and option2_value is S
        $variant = \App\Models\ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Slim Tube Top Long Dress Fashion Bandeau',
            'sku' => 'SKU-DRESS-1-BLK-S',
            'selling_price' => 25.00,
            'option1_name' => 'Color',
            'option1_value' => 'Black',
            'option2_name' => 'Size',
            'option2_value' => 'S',
            'status' => 'active'
        ]);

        $this->assertEquals('Black · S', $variant->display_name);
    }
}


