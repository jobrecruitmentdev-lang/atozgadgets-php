<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductMedia;
use App\Services\Cj\CjProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VariantImageAndNameIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_cj_product_details_uses_contextual_fallback_name_and_image()
    {
        $details = CjProductService::getProductDetails(
            'TEST-PID-999',
            'Halloween Fabric Decoration Pendant',
            'https://example.com/halloween-pendant.jpg'
        );

        $this->assertEquals('Halloween Fabric Decoration Pendant', $details['nameEn']);
        $this->assertEquals('https://example.com/halloween-pendant.jpg', $details['mainImage']);
        $this->assertNotEmpty($details['variants']);
        $this->assertEquals('Standard Edition', $details['variants'][0]['variantName']);
        $this->assertEquals('https://example.com/halloween-pendant.jpg', $details['variants'][0]['image']);
    }

    public function test_cart_guards_against_mock_watch_image_and_name_for_decor_items()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin_test@example.com'],
            ['first_name' => 'Admin', 'last_name' => 'Test', 'password' => bcrypt('password')]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'novelty-creative-gifts'],
            ['name' => 'Novelty & Creative Gifts', 'status' => 'active']
        );

        $product = Product::create([
            'name' => 'Halloween Fabric Decoration Pendant',
            'slug' => 'halloween-fabric-decoration-pendant',
            'sku' => 'SKU-HAL-01',
            'category_id' => $category->id,
            'price' => 21.50,
            'thumbnail_image' => 'https://example.com/halloween-pendant.jpg',
            'stock_quantity' => 50,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        // Variant has mock watch image and mock watch variant name
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-HAL-01-V1',
            'name' => 'Midnight Black / Standard',
            'selling_price' => 21.50,
            'cost_price' => 10.00,
            'stock_quantity' => 50,
            'status' => 'active',
            'image_url' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?q=80&w=800',
        ]);

        // Add to cart
        $response = $this->post(route('store.cart.add'), [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1
        ]);

        $response->assertSessionHasNoErrors();
        $cart = session()->get('cart', []);
        $cartKey = "{$product->id}_{$variant->id}";

        $this->assertArrayHasKey($cartKey, $cart);
        // Image should be guarded and reverted to product's actual thumbnail!
        $this->assertEquals($product->customer_thumbnail, $cart[$cartKey]['image']);
        // Variant name should be cleansed to Standard!
        $this->assertEquals('Standard', $cart[$cartKey]['variant_name']);
    }

    public function test_cart_sync_auto_heals_poisoned_session_cart()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin_test2@example.com'],
            ['first_name' => 'Admin', 'last_name' => 'Test', 'password' => bcrypt('password')]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'smart-lighting-lamps'],
            ['name' => 'Smart Lighting & Lamps', 'status' => 'active']
        );

        $product = Product::create([
            'name' => 'Solar Garden Lantern Decor',
            'slug' => 'solar-garden-lantern-decor',
            'sku' => 'SKU-LAN-01',
            'category_id' => $category->id,
            'price' => 18.00,
            'thumbnail_image' => 'https://example.com/lantern.jpg',
            'stock_quantity' => 50,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        // Pre-fill session with a poisoned watch image from earlier
        session()->put('cart', [
            "{$product->id}_0" => [
                'product_id' => $product->id,
                'name' => $product->name,
                'variant_name' => 'Midnight Black / Standard',
                'price' => 18.00,
                'quantity' => 2,
                'image' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12',
            ]
        ]);

        $response = $this->get(route('store.cart'));
        $response->assertStatus(200);

        $cart = session()->get('cart', []);
        $this->assertEquals($product->customer_thumbnail, $cart["{$product->id}_0"]['image']);
        $this->assertEquals('Standard', $cart["{$product->id}_0"]['variant_name']);
    }

    public function test_migration_repairs_mismatched_variant_images_and_names()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin_test3@example.com'],
            ['first_name' => 'Admin', 'last_name' => 'Test', 'password' => bcrypt('password')]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'novelty-creative-gifts'],
            ['name' => 'Novelty & Creative Gifts', 'status' => 'active']
        );

        $product = Product::create([
            'name' => 'Halloween Pendant Decor Lamp',
            'slug' => 'halloween-pendant-decor-lamp',
            'sku' => 'SKU-HAL-LAMP',
            'category_id' => $category->id,
            'price' => 21.50,
            'thumbnail_image' => 'https://example.com/pendant-lamp.jpg',
            'stock_quantity' => 50,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-HAL-LAMP-V1',
            'name' => 'Midnight Black / Standard',
            'selling_price' => 21.50,
            'cost_price' => 10.00,
            'stock_quantity' => 50,
            'status' => 'active',
            'image_url' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?q=80&w=800',
        ]);

        // Run the migration class directly
        $migration = require database_path('migrations/2026_09_24_180500_fix_mismatched_variant_images_and_names.php');
        $migration->up();

        $variant->refresh();
        $this->assertEquals('Standard', $variant->name);
        $this->assertEquals('https://example.com/pendant-lamp.jpg', $variant->image_url);
    }
}
