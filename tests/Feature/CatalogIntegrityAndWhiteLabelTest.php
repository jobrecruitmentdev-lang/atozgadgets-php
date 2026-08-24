<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductSpecification;
use App\Models\ProductReview;
use App\Models\CjProduct;
use Illuminate\Support\Facades\Http;

class CatalogIntegrityAndWhiteLabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_renders_rich_catalog_and_masks_supplier_cdn()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john_catalog@example.com',
            'mobile' => '9988776655',
            'password' => bcrypt('password')
        ]);

        $category = Category::create([
            'name' => 'Music Accessories',
            'slug' => 'music-accessories',
            'status' => 'active'
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => '5 Core Professional Guitar Picks 12-Pack',
            'slug' => '5-core-guitar-picks-12-pack',
            'sku' => 'GP-5C-012',
            'price' => 18.99,
            'discount_price' => 14.99,
            'description' => 'Premium celluloid guitar picks offering warm tone, classic feel, and durable grip.',
            'thumbnail_image' => 'https://cf.cjdropshipping.com/supplier/guitar-picks-main.jpg',
            'stock_quantity' => 50,
            'status' => 'active',
            'is_active' => true,
            'fulfillment_type' => 'cj',
            'created_by' => $user->id
        ]);

        ProductMedia::create([
            'product_id' => $product->id,
            'type' => 'image',
            'url' => 'https://cf.cjdropshipping.com/supplier/guitar-picks-gallery-1.jpg',
            'alt_text' => 'Grip Texture Detail',
            'sort_order' => 1,
        ]);

        ProductSpecification::create([
            'product_id' => $product->id,
            'name' => 'Material',
            'value' => 'Premium Celluloid',
            'sort_order' => 1,
        ]);

        ProductReview::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Excellent grip and tone',
            'review' => 'Great quality picks, perfect flexibility and comfortable grip for acoustic strumming.',
            'status' => 'approved',
            'verified_purchase' => true
        ]);

        $response = $this->get(route('store.product', $product->slug));

        $response->assertStatus(200);

        // 1. Assert NO raw CJ CDN domains in public HTML
        $response->assertDontSee('cf.cjdropshipping.com');
        $response->assertDontSee('cjdropshipping.com');

        // 2. Assert NO fake wearables template description contamination
        $response->assertDontSee('smart wearables');
        $response->assertDontSee('bluetooth calling');
        $response->assertSee('Premium celluloid guitar picks');

        // 3. Assert rich specs & reviews rendered
        $response->assertSee('Premium Celluloid');
        $response->assertSee('Excellent grip and tone');
        $response->assertSee('Verified Purchase');
        $response->assertSee('Standard Delivery');
    }

    public function test_customer_can_submit_product_review()
    {
        $user = User::create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice@example.com',
            'mobile' => '9123456780',
            'password' => bcrypt('password')
        ]);

        $category = Category::create(['name' => 'Gadgets', 'slug' => 'gadgets', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'AtoZ Smart Mini Drone 4K',
            'slug' => 'smart-mini-drone-4k',
            'sku' => 'DRONE-4K-01',
            'price' => 89.99,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id
        ]);

        $response = $this->actingAs($user)->post(route('store.product.review', $product->slug), [
            'rating' => 5,
            'title' => 'Incredible camera stability',
            'review' => 'Flight time is surprisingly good and the 4K footage is crisp. Highly recommend!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Incredible camera stability',
            'status' => 'approved'
        ]);
    }

    public function test_media_controller_proxies_external_images()
    {
        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin_media@example.com',
            'mobile' => '9991112223',
            'password' => bcrypt('password')
        ]);

        $category = Category::create(['name' => 'Tech', 'slug' => 'tech-media', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'AtoZ Wireless Charger',
            'slug' => 'wireless-charger',
            'sku' => 'CHG-001',
            'price' => 29.99,
            'thumbnail_image' => 'https://example.com/fake-image.jpg',
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id
        ]);

        Http::fake([
            'https://example.com/fake-image.jpg' => Http::response('fake_image_bytes', 200, ['Content-Type' => 'image/jpeg'])
        ]);

        $response = $this->get(route('media.product.thumbnail', $product->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');
        $this->assertEquals('fake_image_bytes', $response->getContent());
    }

    public function test_footer_contains_clean_pricing_and_no_unverified_claims()
    {
        $response = $this->get(route('store.home'));

        $response->assertStatus(200);

        // Assert unverified warehouse & arbitrary conversion claims are gone
        $response->assertDontSee('50+ global warehouses');
        $response->assertDontSee('100% trusted.');
        $response->assertDontSee('/ ₹99');
        $response->assertDontSee('/ ₹499');

        // Assert clean price brackets exist
        $response->assertSee('Under $10');
        $response->assertSee('Under $50');
        $response->assertSee('Under $100');
    }
}
