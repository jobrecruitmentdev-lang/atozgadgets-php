<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\Shipping\CjShippingEligibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CjShippingEligibilityEngineTest extends TestCase
{
    use RefreshDatabase;

    private $category;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);
        $this->user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Tester',
            'email' => 'admin.shipping@example.com',
            'password' => Hash::make('secret123'),
            'role_id' => 1,
            'is_active' => 1,
        ]);
    }

    public function test_shipping_eligibility_resolves_tier1_destinations_with_carrier_and_eta()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'name' => 'Smart Drone 4K',
            'slug' => 'smart-drone-4k',
            'sku' => 'DRONE-4K',
            'price' => 99.99,
            'selling_price' => 99.99,
            'stock_quantity' => 10,
            'fulfillment_type' => 'cj',
            'status' => 'active',
        ]);

        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'price' => 99.99,
            ]
        ];

        // 1. United States (US)
        $usResult = CjShippingEligibilityService::checkEligibility($cart, 'US');
        $this->assertTrue($usResult['eligible']);
        $this->assertEquals('US', $usResult['country']);
        $this->assertNotEmpty($usResult['carrier']);
        $this->assertNotEmpty($usResult['eta']);
        $this->assertNotEmpty($usResult['warehouse']);

        // 2. United Kingdom (GB)
        $gbResult = CjShippingEligibilityService::checkEligibility($cart, 'GB');
        $this->assertTrue($gbResult['eligible']);
        $this->assertEquals('GB', $gbResult['country']);
        $this->assertStringContainsString('Royal Mail', $gbResult['carrier']);

        // 3. Canada (CA)
        $caResult = CjShippingEligibilityService::checkEligibility($cart, 'CA');
        $this->assertTrue($caResult['eligible']);
        $this->assertEquals('CA', $caResult['country']);

        // 4. Australia (AU)
        $auResult = CjShippingEligibilityService::checkEligibility($cart, 'AU');
        $this->assertTrue($auResult['eligible']);
        $this->assertEquals('AU', $auResult['country']);
    }

    public function test_shipping_eligibility_blocks_restricted_or_unsupported_destinations()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'name' => 'Smart Drone 4K',
            'slug' => 'smart-drone-4k',
            'sku' => 'DRONE-4K',
            'price' => 99.99,
            'selling_price' => 99.99,
            'stock_quantity' => 10,
            'fulfillment_type' => 'cj',
            'status' => 'active',
        ]);

        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'price' => 99.99,
            ]
        ];

        // Testing restricted/invalid destination (e.g. North Korea / KP)
        $res = CjShippingEligibilityService::checkEligibility($cart, 'KP');
        $this->assertFalse($res['eligible']);
        $this->assertStringContainsString('unavailable', strtolower($res['message']));
    }

    public function test_checkout_check_eligibility_endpoint_returns_json()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'name' => 'Smart Watch Pro',
            'slug' => 'smart-watch-pro',
            'sku' => 'WATCH-PRO',
            'price' => 49.99,
            'selling_price' => 49.99,
            'stock_quantity' => 20,
            'fulfillment_type' => 'cj',
            'status' => 'active',
        ]);

        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'price' => 49.99,
            ]
        ];

        $response = $this->withSession(['cart' => $cart])
            ->postJson(route('store.checkout.eligibility'), ['country' => 'US']);

        $response->assertStatus(200);
        $response->assertJson([
            'eligible' => true,
            'country' => 'US',
        ]);
        $response->assertJsonStructure(['eligible', 'country', 'carrier', 'eta', 'warehouse', 'message']);
    }

    public function test_paypal_create_order_blocks_ineligible_destination_authoritatively()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'name' => 'Restricted Gadget',
            'slug' => 'restricted-gadget',
            'sku' => 'RESTRICTED-1',
            'price' => 79.99,
            'selling_price' => 79.99,
            'stock_quantity' => 5,
            'fulfillment_type' => 'cj',
            'status' => 'active',
        ]);

        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'price' => 79.99,
            ]
        ];

        // Attempting to checkout to unsupported country KP
        $response = $this->withSession(['cart' => $cart])
            ->postJson(route('payment.paypal.create'), [
                'address' => [
                    'country' => 'KP',
                    'city' => 'Pyongyang',
                    'state' => 'Pyongyang',
                    'address1' => '100 Main St',
                    'postal_code' => '00000',
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'phone' => '+1234567890',
                    'email' => 'test@example.com',
                ]
            ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['error']);
        $this->assertDatabaseMissing('orders', ['contact_email' => 'test@example.com']);
    }
}
