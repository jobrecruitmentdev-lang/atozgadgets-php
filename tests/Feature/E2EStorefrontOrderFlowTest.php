<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\CjProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class E2EStorefrontOrderFlowTest extends TestCase
{
    use RefreshDatabase;
    public function test_complete_e2e_storefront_to_fulfillment_lifecycle()
    {
        Mail::fake();

        // 0. Ensure Admin User 1 exists and CJ test configuration is set
        $user = User::firstOrCreate(
            ['id' => 1],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@atozgadgets.test',
                'mobile' => '1234567890',
                'role_id' => 1,
                'password' => bcrypt('secret123')
            ]
        );

        \App\Models\Setting::set('cj_sandbox_mode', '0', 'cj');
        \App\Models\Setting::set('cj_api_email', 'admin@example.com', 'cj');
        \App\Models\Setting::set('cj_api_key', 'test_key_123', 'cj');
        \Illuminate\Support\Facades\Cache::put('cj_access_token', 'fake_e2e_token', 600);

        // 1. Seed Categories and Active Products
        $category = Category::firstOrCreate(
            ['slug' => 'gadgets-e2e'],
            ['name' => 'E2E Smart Gadgets', 'status' => 'active']
        );

        $product = Product::firstOrCreate(
            ['slug' => 'smart-lamp-e2e'],
            [
                'category_id' => $category->id,
                'name' => 'AtoZ Smart 3-in-1 Desk Lamp',
                'sku' => 'CJ-LAMP-E2E-01',
                'price' => 49.99,
                'discount_price' => 39.99,
                'stock_quantity' => 50,
                'status' => 'active',
                'is_active' => true,
                'fulfillment_type' => 'cj',
                'created_by' => 1
            ]
        );

        CjProduct::updateOrCreate(
            ['cj_product_id' => 'CJ-PID-E2E-999'],
            [
                'internal_product_id' => $product->id,
                'title' => $product->name,
                'sell_price' => 15.00,
                'status' => 'imported'
            ]
        );

        // 2. Storefront Home & Product View
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);

        $productRes = $this->get('/product/' . $product->slug);
        $productRes->assertStatus(200)
                   ->assertSee('AtoZ Smart 3-in-1 Desk Lamp');

        // 3. Add to Cart
        $cartAddRes = $this->post('/cart/add', [
            'product_id' => $product->id
        ]);
        $cartAddRes->assertRedirect();
        $cart = session('cart');
        $cartItem = reset($cart);
        $this->assertEquals(1, $cartItem['quantity']);
        $this->assertEquals($product->id, $cartItem['product_id']);

        // 4. Send & Verify OTP for Checkout
        $shippingPayload = [
            'first_name' => 'Alex',
            'last_name' => 'Developer',
            'email' => 'alex@example.com',
            'phone' => '+15551234567',
            'address1' => '742 Evergreen Terrace',
            'address2' => 'Apt 4B',
            'city' => 'Springfield',
            'state' => 'OR',
            'postal_code' => '97477',
            'country' => 'US'
        ];

        $otpRes = $this->postJson('/checkout/send-otp', $shippingPayload);
        $otpRes->assertStatus(200)->assertJson(['success' => true]);

        $sessionOtp = session('checkout_otp');
        $this->assertNotEmpty($sessionOtp);

        $verifyOtpRes = $this->postJson('/checkout/verify-otp', ['otp' => $sessionOtp]);
        $verifyOtpRes->assertStatus(200)->assertJson(['success' => true]);
        $this->assertTrue(session('checkout_otp_verified'));

        // 5. Complete Storefront Checkout (Atomically creates Order + OrderItems + Payment)
        $checkoutCjId = 'CJ-CHECKOUT-' . uniqid();
        Http::fake([
            '*/shopping/order/createOrderV2*' => Http::response([
                'code' => 200,
                'result' => true,
                'data' => ['orderId' => $checkoutCjId]
            ], 200),
            '*/shopping/order/submitOrder*' => Http::response([
                'code' => 200,
                'result' => true
            ], 200),
            '*/logistic/freightCalculate*' => Http::response([
                'code' => 200,
                'data' => [['logisticName' => 'CJPacket Fast Line']]
            ], 200),
            '*/shoppingSync/synchronousOrder*' => Http::response(['code' => 200, 'result' => true], 200)
        ]);

        $checkoutRes = $this->withSession([
            'cart' => [
                $product->id => [
                    'name' => $product->name,
                    'quantity' => 1,
                    'price' => $product->discount_price ?? $product->price,
                    'image' => $product->thumbnail_image
                ]
            ],
            'checkout_shipping' => $shippingPayload,
            'checkout_otp_verified' => true
        ])->post('/checkout', [
            'payment_method' => 'paypal'
        ]);
        
        $order = Order::orderBy('id', 'desc')->first();
        $this->assertNotNull($order);

        // 5. Complete PayPal Payment Capture
        \App\Services\Order\OrderService::markAsPaid($order);
        \App\Models\Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'payment_method' => 'paypal',
            'status' => 'success',
            'transaction_id' => 'CAP-E2E-TEST-123'
        ]);
        $order->refresh();

        // 6. ADBMS Database Integrity & Relationship Assertions
        $this->assertEquals('processing', $order->status);
        $this->assertEquals('paid', $order->payment_status);

        // Verify OrderItem records exist and relate to Product & Order
        $this->assertCount(1, $order->items);
        $orderItem = $order->items->first();
        $this->assertEquals($product->id, $orderItem->product_id);
        $this->assertEquals($order->id, $orderItem->order->id);
        $this->assertEquals($product->name, $orderItem->product->name);
        $this->assertEquals('CJ-PID-E2E-999', $orderItem->product->cjProduct->cj_product_id);

        // Verify Payment relationship
        $this->assertNotNull($order->payment);
        $this->assertEquals('paypal', $order->payment->payment_method);
        $this->assertEquals($order->id, $order->payment->order->id);

        // Verify Structured Address Accessor
        $address = $order->address;
        $this->assertNotNull($address);
        $this->assertEquals('US', $address->country);
        $this->assertEquals('742 Evergreen Terrace', $address->address_line1);
        $this->assertEquals('Springfield', $address->city);

        $admin = User::firstOrCreate(
            ['email' => 'admin_e2e_suite@example.com'],
            ['first_name' => 'Admin', 'last_name' => 'User', 'mobile' => '9999999999', 'password' => bcrypt('secret123'), 'role_id' => 1]
        );
        $admin->role_id = 1;
        $admin->save();

        \App\Models\Setting::set('cj_sandbox_mode', '0', 'cj');
        \App\Models\Setting::set('cj_api_email', 'admin@example.com', 'cj');
        \App\Models\Setting::set('cj_api_key', 'test_key_123', 'cj');
        \Illuminate\Support\Facades\Cache::put('cj_access_token', 'fake_e2e_token', 600);

        $mockLiveCjId = 'CJ-LIVE-ORDER-' . uniqid();
        \Illuminate\Support\Facades\Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake([
            '*/authentication/getAccessToken*' => Http::response([
                'code' => 200,
                'result' => true,
                'data' => ['accessToken' => 'fake_e2e_token']
            ], 200),
            '*/shopping/order/createOrderV2*' => Http::response([
                'code' => 200,
                'result' => true,
                'data' => ['orderId' => $mockLiveCjId]
            ], 200),
            '*/shopping/order/submitOrder*' => Http::response([
                'code' => 200,
                'result' => true
            ], 200),
            '*/logistic/freightCalculate*' => Http::response([
                'code' => 200,
                'data' => [['logisticName' => 'CJPacket Fast Line']]
            ], 200)
        ]);

        $fulfillRes = $this->actingAs($admin, 'sanctum')
                           ->postJson("/api/admin/cj/orders/{$order->id}/place");

        $fulfillRes->assertStatus(200)->assertJson(['success' => true]);

        // Verify CjOrder mapping
        $cjOrder = \App\Models\CjOrder::where('internal_order_id', $order->id)->first();
        $this->assertNotNull($cjOrder);
        $this->assertEquals($mockLiveCjId, $cjOrder->cj_order_id);
        $this->assertEquals($order->id, $cjOrder->order->id);

        // 8. Test CJ Webhook Tracking Sync
        Shipment::create([
            'order_id' => $order->id,
            'tracking_number' => null,
            'carrier' => null,
            'status' => 'pending'
        ]);

        $webhookPayload = [
            'orderNumber' => $order->order_number,
            'orderStatus' => 'shipped',
            'trackingNumber' => 'CJTRK9876543210',
            'carrierName' => 'CJPacket Fast Line'
        ];

        $webhookRes = $this->postJson('/api/cj/webhook', $webhookPayload);
        $webhookRes->assertStatus(200)->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals('shipped', $order->status);
        $this->assertEquals('CJTRK9876543210', $order->shipment->tracking_number);
        $this->assertEquals('CJPacket Fast Line', $order->shipment->carrier);
    }
}
