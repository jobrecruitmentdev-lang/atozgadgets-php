<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductVariant;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CjProduct;
use App\Models\Setting;
use App\Services\Catalog\ProductContentService;
use App\Services\Inventory\InventoryService;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Support\Facades\DB;

class ProductPageHardeningPillarsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'first_name' => 'John',
            'last_name' => 'Tester',
            'email' => 'john.test@example.com',
            'mobile' => '9876543210',
            'password' => bcrypt('secret123'),
        ]);

        $this->category = Category::create([
            'name' => 'Music & Instruments',
            'slug' => 'music-instruments',
            'status' => 'active'
        ]);
    }

    /**
     * Pillar 1: Multi-image gallery with ProductMedia support
     */
    public function test_pillar_1_product_media_gallery_renders_multiple_images_and_thumbnails()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'AtoZ Acoustic Guitar Pick Set',
            'slug' => 'acoustic-guitar-pick-set',
            'sku' => 'AZG-MUS-001',
            'price' => 19.99,
            'stock_quantity' => 20,
            'status' => 'active',
            'is_active' => true,
            'thumbnail_image' => 'https://example.com/main.jpg',
            'created_by' => $this->user->id,
        ]);

        ProductMedia::create([
            'product_id' => $product->id,
            'type' => 'image',
            'url' => 'https://example.com/view1.jpg',
            'alt_text' => 'View 1',
            'sort_order' => 1,
            'is_primary' => false,
        ]);

        ProductMedia::create([
            'product_id' => $product->id,
            'type' => 'image',
            'url' => 'https://example.com/view2.jpg',
            'alt_text' => 'View 2',
            'sort_order' => 2,
            'is_primary' => false,
        ]);

        $response = $this->get(route('store.product', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('thumbnails-strip');
        $response->assertSee('switchMainImage', false);
    }

    /**
     * Pillar 2: ProductContentService cleans and validates content & catches category mismatches
     */
    public function test_pillar_2_product_content_service_normalizes_and_strips_supplier_branding_and_mismatch()
    {
        // 1. Test Title Normalization
        $rawTitle = "  CJZN2877369 Wholesale cjdropshipping dropshipping Guitar Pick Set OEM/ODM   ";
        $cleanTitle = ProductContentService::normalizeTitle($rawTitle, 'Music & Instruments');
        $this->assertStringNotContainsString('cjdropshipping', strtolower($cleanTitle));
        $this->assertStringNotContainsString('CJZN2877369', $cleanTitle);
        $this->assertEquals('Guitar Pick Set', $cleanTitle);

        // 2. Test Description Normalization with Disjoint Category Conflict (Guitar picks with smartwatch copy)
        $mismatchDescription = "Experience the next generation of smart wearables with heart rate monitor, pedometer, and AMOLED sleep tracking.";
        $normalizedDesc = ProductContentService::normalizeDescription($mismatchDescription, 'Guitar Pick Set', 'Music & Instruments');
        $this->assertStringNotContainsString('heart rate', $normalizedDesc);
        $this->assertStringNotContainsString('smart wearables', $normalizedDesc);
        $this->assertStringContainsString('musicians', $normalizedDesc);
    }

    /**
     * Pillar 3: Verified reviews require a completed/paid order, no fake social proof by default
     */
    public function test_pillar_3_review_verified_purchase_requires_paid_order_and_zero_fake_reviews_by_default()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'AtoZ Titanium Capo',
            'slug' => 'titanium-capo',
            'sku' => 'AZG-MUS-002',
            'price' => 29.99,
            'stock_quantity' => 10,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        // Default state: 0 reviews
        $this->assertEquals(0, $product->review_count);
        $response = $this->get(route('store.product', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('0 customer reviews');
        $response->assertSee('No customer reviews yet for this product.');

        // User without paid order submits review -> verified_purchase = false
        $this->actingAs($this->user);
        $this->post(route('store.product.review', $product->slug), [
            'rating' => 5,
            'title' => 'Looks great',
            'review' => 'Excited to try this product soon.',
        ]);

        $unverifiedReview = ProductReview::where('product_id', $product->id)->first();
        $this->assertNotNull($unverifiedReview);
        $this->assertFalse((bool)$unverifiedReview->verified_purchase);

        // User creates and pays for order
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-REV-101',
            'subtotal' => 29.99,
            'total_amount' => 29.99,
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 29.99,
            'total_price' => 29.99,
        ]);

        // Now submits another review -> verified_purchase = true
        $this->post(route('store.product.review', $product->slug), [
            'rating' => 5,
            'title' => 'Verified Excellent Quality',
            'review' => 'Received and tested. Perfect intonation and solid build.',
        ]);

        $verifiedReview = ProductReview::where('product_id', $product->id)->latest('id')->first();
        $this->assertTrue((bool)$verifiedReview->verified_purchase);
    }

    /**
     * Pillar 4: Dynamic inventory evaluates both quantity and sync freshness
     */
    public function test_pillar_4_dynamic_inventory_checks_both_quantity_and_sync_freshness()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'AtoZ Studio Headphones',
            'slug' => 'studio-headphones',
            'sku' => 'AZG-AUD-003',
            'price' => 89.99,
            'stock_quantity' => 25,
            'fulfillment_type' => 'cj',
            'status' => 'active',
            'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        // Fresh sync with stock > 5 -> IN_STOCK
        $avail = InventoryService::getAvailability($product);
        $this->assertEquals(InventoryService::STATUS_IN_STOCK, $avail['status']);
        $this->assertTrue($avail['is_purchasable']);

        // Stock <= 5 -> LOW_STOCK
        $product->update(['stock_quantity' => 3]);
        $availLow = InventoryService::getAvailability($product);
        $this->assertEquals(InventoryService::STATUS_LOW_STOCK, $availLow['status']);
        $this->assertStringContainsString('3', $availLow['label']);

        // Stock 0 -> OUT_OF_STOCK
        $product->update(['stock_quantity' => 0]);
        $availOut = InventoryService::getAvailability($product);
        $this->assertEquals(InventoryService::STATUS_OUT_OF_STOCK, $availOut['status']);
        $this->assertFalse($availOut['is_purchasable']);

        // Stale sync (e.g. updated 5 days ago) with stock -> CONFIRMING
        DB::table('products')->where('id', $product->id)->update([
            'stock_quantity' => 50,
            'updated_at' => now()->subDays(5)->toDateTimeString()
        ]);
        $product->refresh();

        $availConfirming = InventoryService::getAvailability($product);
        $this->assertEquals(InventoryService::STATUS_CONFIRMING, $availConfirming['status']);
        $this->assertEquals('Availability being confirmed', $availConfirming['label']);
    }

    /**
     * Pillar 5: Shipping Abstraction returns realistic promises without supplier leaks
     */
    public function test_pillar_5_shipping_promise_abstraction_returns_truthful_eta_without_supplier_leaks()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'AtoZ Smart Tuner',
            'slug' => 'smart-tuner',
            'sku' => 'AZG-MUS-004',
            'price' => 24.99,
            'stock_quantity' => 15,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals('Standard Delivery: 7–15 Business Days', $product->shipping_promise);

        $response = $this->get(route('store.product', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('7–15 business days', false);
        $response->assertDontSee('CJPacket');
        $response->assertDontSee('supplier');
    }

    /**
     * Pillar 6: Merchant SKU generation and supplier ID customer isolation
     */
    public function test_pillar_6_merchant_sku_generation_and_strict_supplier_id_isolation()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'AtoZ Pro Drone',
            'slug' => 'pro-drone',
            'sku' => 'CJ-SECRET-SUPPLIER-PID-999', // Supplier-derived SKU
            'price' => 199.99,
            'stock_quantity' => 10,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $this->user->id,
        ]);

        CjProduct::create([
            'cj_product_id' => 'CJ-SECRET-SUPPLIER-PID-999',
            'internal_product_id' => $product->id,
            'sell_price' => 70.00,
            'status' => 'imported',
        ]);

        // Accessor automatically transforms into customer-safe merchant SKU
        $this->assertStringStartsWith('AZG-MUS-', $product->merchant_sku);
        $this->assertStringNotContainsString('CJ-SECRET', $product->merchant_sku);

        $response = $this->get(route('store.product', $product->slug));
        $response->assertStatus(200);
        $response->assertSee($product->merchant_sku);
        $response->assertDontSee('CJ-SECRET-SUPPLIER-PID-999');
    }

    /**
     * Pillar 7: Dynamic payment gateway manager reflects active environment and config
     */
    public function test_pillar_7_payment_gateway_manager_dynamically_renders_active_customer_methods()
    {
        Setting::set('paypal_mode', 'sandbox', 'payment');
        Setting::set('paypal_sandbox_client_id', 'sb_client_id_123', 'payment');

        $methods = PaymentGatewayManager::customerAvailableMethods();
        $this->assertNotEmpty($methods);
        $this->assertEquals('paypal', $methods[0]['id']);
        $this->assertEquals('PayPal (Sandbox)', $methods[0]['badge']);

        $headline = PaymentGatewayManager::getTrustHeadline();
        $this->assertStringContainsString('PayPal', $headline);
    }

    /**
     * Pillar 8: Product Import Validation Gate rejects incomplete products to draft
     */
    public function test_pillar_8_product_import_validation_gate_fails_invalid_items_to_draft()
    {
        // Missing name & price <= 0 -> fails validation
        $invalidData = [
            'name' => '',
            'description' => 'Short',
            'category_id' => null,
            'price' => 0,
        ];

        $gateResult = ProductContentService::validateForPublish($invalidData);
        $this->assertFalse($gateResult['can_publish']);
        $this->assertEquals('draft', $gateResult['status']);
        $this->assertNotEmpty($gateResult['errors']);

        // Complete valid data -> passes validation
        $validData = [
            'name' => 'AtoZ Ultra Power Bank 20000mAh',
            'description' => 'High capacity power bank equipped with dual USB-C fast charging ports.',
            'category_id' => $this->category->id,
            'thumbnail_image' => 'https://example.com/powerbank.jpg',
            'price' => 45.99,
        ];

        $validGateResult = ProductContentService::validateForPublish($validData);
        $this->assertTrue($validGateResult['can_publish']);
        $this->assertEquals('active', $validGateResult['status']);
    }
}