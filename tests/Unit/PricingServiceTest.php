<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\User;
use App\Services\Catalog\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            "first_name" => "Pricing",
            "last_name" => "Tester",
            "email" => "pricing@atozgadgets.com",
            "mobile" => "+1999888777",
            "password" => bcrypt("secret"),
            "role_id" => 1
        ]);
        $this->category = Category::create(["name" => "Electronics", "slug" => "electronics"]);
    }

    private function createProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            "category_id" => $this->category->id,
            "name" => "Test Gadget",
            "slug" => "test-gadget-" . uniqid(),
            "sku" => "SKU-" . uniqid(),
            "price" => 100.00,
            "discount_price" => null,
            "created_by" => $this->user->id
        ], $overrides));
    }

    /** @test */
    public function test_1_valid_discount_price_lower_than_regular_price()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => 80.00]);
        $this->assertEquals(80.00, PricingService::resolveCustomerPrice($product));
        $this->assertEquals(80.00, $product->effective_price);
        $this->assertTrue(PricingService::hasActiveDiscount($product));
        $this->assertTrue($product->has_active_discount);
    }

    /** @test */
    public function test_2_null_discount_price_uses_regular_price()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => null]);
        $this->assertEquals(100.00, PricingService::resolveCustomerPrice($product));
        $this->assertEquals(100.00, $product->effective_price);
        $this->assertFalse(PricingService::hasActiveDiscount($product));
        $this->assertFalse($product->has_active_discount);
    }

    /** @test */
    public function test_3_inverted_discount_higher_than_regular_price_falls_back_to_regular()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => 120.00]);
        $this->assertEquals(100.00, PricingService::resolveCustomerPrice($product));
        $this->assertEquals(100.00, $product->effective_price);
        $this->assertFalse(PricingService::hasActiveDiscount($product));
        $this->assertFalse($product->has_active_discount);
    }

    /** @test */
    public function test_4_equal_discount_and_regular_price_falls_back_to_regular()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => 100.00]);
        $this->assertEquals(100.00, PricingService::resolveCustomerPrice($product));
        $this->assertEquals(100.00, $product->effective_price);
        $this->assertFalse(PricingService::hasActiveDiscount($product));
        $this->assertFalse($product->has_active_discount);
    }

    /** @test */
    public function test_5_zero_discount_price_falls_back_to_regular_price()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => 0.00]);
        $this->assertEquals(100.00, PricingService::resolveCustomerPrice($product));
        $this->assertEquals(100.00, $product->effective_price);
        $this->assertFalse(PricingService::hasActiveDiscount($product));
        $this->assertFalse($product->has_active_discount);
    }

    /** @test */
    public function test_6_valid_variant_selling_price_overrides_discount_and_regular_price()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => 80.00]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Premium Edition',
            'sku' => 'SKU-PREM',
            'selling_price' => 70.00,
            'stock_quantity' => 10,
        ]);

        $this->assertEquals(70.00, PricingService::resolveCustomerPrice($product, $variant));
    }

    /** @test */
    public function test_7_zero_variant_selling_price_falls_back_to_product_discount_price()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => 80.00]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Standard Edition',
            'sku' => 'SKU-STD',
            'selling_price' => 0.00,
            'stock_quantity' => 10,
        ]);

        $this->assertEquals(80.00, PricingService::resolveCustomerPrice($product, $variant));
    }

    /** @test */
    public function test_8_null_variant_selling_price_falls_back_to_product_discount_price()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => 80.00]);
        $this->assertEquals(80.00, PricingService::resolveCustomerPrice($product, null));
    }

    /** @test */
    public function test_9_zero_base_and_zero_discount_returns_zero()
    {
        $product = $this->createProduct(['price' => 0.00, 'discount_price' => 0.00]);
        $this->assertEquals(0.00, PricingService::resolveCustomerPrice($product));
        $this->assertFalse(PricingService::hasActiveDiscount($product));
    }

    /** @test */
    public function test_10_negative_discount_price_falls_back_to_regular_price()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => -20.00]);
        $this->assertEquals(100.00, PricingService::resolveCustomerPrice($product));
        $this->assertFalse(PricingService::hasActiveDiscount($product));
    }

    /** @test */
    public function test_cart_and_checkout_use_centralized_authoritative_price()
    {
        $product = $this->createProduct(['price' => 100.00, 'discount_price' => 75.00]);
        
        $response = $this->post(route('store.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 0.01
        ]);

        $response->assertSessionHas('success');
        $cart = session('cart');
        $this->assertNotEmpty($cart);
        $cartItem = reset($cart);
        
        $this->assertEquals(75.00, $cartItem['price']);
    }
}
