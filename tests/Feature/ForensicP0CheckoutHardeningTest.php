<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use App\Services\Catalog\PricingService;
use App\Services\Checkout\CheckoutService;
use App\Services\Order\OrderService;
use App\Services\Payment\PaymentService;
use App\Services\Shipping\ShippingService;
use App\Services\Tax\TaxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class ForensicP0CheckoutHardeningTest extends TestCase
{
    use RefreshDatabase;

    private $category;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('free_shipping_threshold', 50.00);
        Setting::set('standard_shipping_rate', 5.99);

        $this->category = Category::create([
            'name' => 'Smart Gadgets',
            'slug' => 'smart-gadgets',
        ]);

        $this->user = User::create([
            'first_name' => 'Forensic',
            'last_name' => 'Tester',
            'email' => 'forensic@example.com',
            'password' => Hash::make('Secret123!'),
            'role_id' => 3,
            'is_active' => 1,
        ]);

        // Mock external PayPal Gateway endpoints for deterministic amount verification
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'MOCK_OAUTH_TOKEN_TEST'], 200),
            '*/v2/checkout/orders' => function (\Illuminate\Http\Client\Request $request) {
                $body = json_decode($request->body(), true);
                $amount = $body['purchase_units'][0]['amount']['value'] ?? '0.00';
                return Http::response([
                    'id' => 'PAYPAL_ORDER_' . Str::random(12),
                    'status' => 'CREATED',
                    'amount' => $amount,
                ], 200);
            },
        ]);
    }

    /**
     * P0 TEST 3: Four-Way Amount Forensic Equality & Threshold Test
     */
    public function test_four_way_amount_equality_and_free_shipping_threshold_matrix()
    {
        $testCases = [
            ['subtotal' => 29.99, 'expected_shipping' => 5.99],
            ['subtotal' => 30.00, 'expected_shipping' => 5.99],
            ['subtotal' => 35.00, 'expected_shipping' => 5.99],
            ['subtotal' => 49.99, 'expected_shipping' => 5.99],
            ['subtotal' => 50.00, 'expected_shipping' => 0.00],
            ['subtotal' => 50.01, 'expected_shipping' => 0.00],
        ];

        $address = [
            'country' => 'US',
            'city' => 'New York',
            'state' => 'NY',
            'address1' => '350 5th Ave',
            'postal_code' => '10118',
            'first_name' => 'Forensic',
            'last_name' => 'Tester',
            'phone' => '+12125550199',
            'email' => 'forensic@example.com',
        ];

        foreach ($testCases as $index => $tc) {
            $product = Product::create([
                'category_id' => $this->category->id,
                'created_by' => $this->user->id,
                'name' => "Threshold Item {$index}",
                'slug' => "threshold-item-{$index}",
                'sku' => "TH-ITEM-{$index}",
                'price' => $tc['subtotal'],
                'selling_price' => $tc['subtotal'],
                'stock_quantity' => 50,
                'fulfillment_type' => 'cj',
                'status' => 'active',
            ]);

            $rawCart = [
                $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => 1,
                    'price' => $tc['subtotal'],
                ]
            ];

            // 1. Shipping Service Authoritative Output
            $calculatedShipping = ShippingService::calculateShipping($tc['subtotal'], $address);
            $this->assertEquals($tc['expected_shipping'], $calculatedShipping, "Shipping calculation failed for subtotal {$tc['subtotal']}");

            // 2. Tax Calculation
            $tax = TaxService::calculateTax($tc['subtotal'], $address);
            $expectedGrandTotal = round($tc['subtotal'] + $tc['expected_shipping'] + $tax, 2);

            // 3. Checkout Session Creation
            $session = CheckoutService::createSession($this->user->id, $rawCart, $address);
            $this->assertEquals($tc['subtotal'], (float)$session->subtotal);
            $this->assertEquals($tc['expected_shipping'], (float)$session->shipping);
            $this->assertEquals($tax, (float)$session->tax);
            $this->assertEquals($expectedGrandTotal, (float)$session->grand_total);

            // 4. Order DB Record Creation
            $order = OrderService::createPendingOrderFromSession($session, $address);
            $this->assertEquals($expectedGrandTotal, (float)$order->total_amount);
            $this->assertEquals($tc['expected_shipping'], (float)$order->shipping_cost);
            $this->assertEquals($tc['subtotal'], (float)$order->net_amount);
            $this->assertEquals($tax, (float)$order->tax_amount);

            // 5. Gateway Payment Intent Amount
            $gatewayOrder = PaymentService::createIntent($order, 'paypal');
            $this->assertEquals($expectedGrandTotal, (float)$gatewayOrder['amount']);

            // 4-WAY INVARIANT PROOF:
            // Cart Subtotal + Shipping + Tax == CheckoutSession == Order DB == PayPal Gateway Amount
            $this->assertEquals((float)$session->grand_total, (float)$order->total_amount);
            $this->assertEquals((float)$order->total_amount, (float)$gatewayOrder['amount']);
            $this->assertEquals($expectedGrandTotal, (float)$gatewayOrder['amount']);
        }
    }

    /**
     * P0 TEST 4: Variant Tampering Forensic Test (Client Injected 0.01 Must Be Overridden by DB)
     */
    public function test_variant_tampering_is_strictly_overridden_by_server_database_price()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'name' => 'AtoZ Smart Watch Series 9',
            'slug' => 'smart-watch-series-9',
            'sku' => 'SW-S9',
            'price' => 149.99,
            'selling_price' => 149.99,
            'stock_quantity' => 20,
            'fulfillment_type' => 'cj',
            'status' => 'active',
        ]);

        $redVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Color: Crimson Red / Size: Large',
            'sku' => 'SW-S9-RED-L',
            'price' => 169.99,
            'selling_price' => 169.99,
            'stock_quantity' => 10,
            'cj_variant_id' => 'CJ-VID-RED-L',
        ]);

        // Malicious client payload: Attempts to buy a $169.99 variant for $0.01 with fake quantity and SKU
        $tamperedCart = [
            $product->id => [
                'product_id' => $product->id,
                'variant_id' => $redVariant->id,
                'name' => 'Hacked Cheap Watch',
                'sku' => 'HACK-SKU-001',
                'price' => 0.01,
                'variant_price' => 0.01,
                'quantity' => 1,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'address1' => '100 Sunset Blvd',
            'postal_code' => '90001',
            'first_name' => 'Tamper',
            'last_name' => 'Tester',
            'phone' => '+12135550188',
            'email' => 'tamper@example.com',
        ];

        // 1. Authoritative Pricing Service Resolution
        $resolvedPrice = PricingService::resolveCustomerPrice($product, $redVariant);
        $this->assertEquals(169.99, $resolvedPrice);

        // 2. Checkout Session ignores client $0.01 and uses authoritative $169.99
        $session = CheckoutService::createSession($this->user->id, $tamperedCart, $address);
        $this->assertEquals(169.99, (float)$session->subtotal);
        $this->assertEquals(0.00, (float)$session->shipping); // Above $50 free shipping threshold
        
        $tax = TaxService::calculateTax(169.99, $address);
        $expectedTotal = round(169.99 + 0.00 + $tax, 2);
        $this->assertEquals($expectedTotal, (float)$session->grand_total);

        // 3. Order Line Item has authoritative DB price and variant name
        $order = OrderService::createPendingOrderFromSession($session, $address);
        $this->assertEquals($expectedTotal, (float)$order->total_amount);

        $lineItem = $order->items->first();
        $this->assertEquals(169.99, (float)$lineItem->unit_price);
        $this->assertEquals('Color: Crimson Red / Size: Large', $lineItem->variant_name_snapshot);
        $this->assertEquals('SW-S9-RED-L', $lineItem->merchant_sku_snapshot);
    }

    /**
     * P0 TEST 5: Inventory Race Condition & Concurrent Checkout Test
     */
    public function test_inventory_race_condition_prevents_overselling_and_negative_stock()
    {
        // 1. Create a product with strictly 1 item in stock
        $limitedProduct = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'name' => 'Ultra Rare Collector Watch',
            'slug' => 'ultra-rare-collector-watch',
            'sku' => 'RARE-001',
            'price' => 999.00,
            'selling_price' => 999.00,
            'stock_quantity' => 1,
            'fulfillment_type' => 'cj',
            'status' => 'active',
        ]);

        $cart = [
            $limitedProduct->id => [
                'product_id' => $limitedProduct->id,
                'name' => $limitedProduct->name,
                'quantity' => 1,
                'price' => 999.00,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'Chicago',
            'state' => 'IL',
            'address1' => '233 S Wacker Dr',
            'postal_code' => '60606',
            'first_name' => 'Concurrent',
            'last_name' => 'Tester',
            'phone' => '+13125550177',
            'email' => 'concurrent@example.com',
        ];

        // Customer A creates session & places pending order (reserving the 1 item)
        $sessionA = CheckoutService::createSession($this->user->id, $cart, $address);
        $orderA = OrderService::createPendingOrderFromSession($sessionA, $address);
        $this->assertNotNull($orderA);
        $this->assertEquals('pending', $orderA->status);

        // Customer B tries to place order for the SAME item
        $sessionB = CheckoutService::createSession($this->user->id, $cart, $address);
        $customerBBlocked = false;

        try {
            OrderService::createPendingOrderFromSession($sessionB, $address);
        } catch (\Throwable $e) {
            $customerBBlocked = true;
            $this->assertStringContainsString('out of stock', strtolower($e->getMessage()));
        }

        // INVARIANTS:
        // 1. Customer B MUST be blocked from checkout
        $this->assertTrue($customerBBlocked, 'Customer B must be rejected due to out of stock.');

        // 2. Customer A completes payment
        OrderService::markAsPaid($orderA);
        $this->assertEquals('processing', $orderA->fresh()->status);

        // 3. Stock in DB must be exactly 0 (NEVER -1)
        $refreshedProduct = Product::find($limitedProduct->id);
        $this->assertEquals(0, $refreshedProduct->stock_quantity, 'Stock quantity must never drop below 0.');
    }
}
