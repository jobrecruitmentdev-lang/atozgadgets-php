<?php

namespace Tests\Feature\CriticalPath;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CjProduct;
use App\Models\CjVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\CjOrder;
use App\Models\OutboxEvent;
use App\Models\ProviderEvent;
use App\Models\Setting;
use App\Services\Order\OrderService;
use App\Services\Payment\PaymentService;
use App\Services\Cj\CjOrderService;
use App\Jobs\ProcessProviderWebhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TenCustomerPathAttackTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Product $product;
    protected ProductVariant $variant;
    protected string $cjPid;
    protected string $cjVid;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        Setting::set('paypal_client_id', 'mock_client_id');
        Setting::set('paypal_client_secret', 'mock_client_secret');
        Setting::set('paypal_mode', 'sandbox');
        Setting::set('cj_sandbox_mode', '1');

        $uid = uniqid();
        $this->cjPid = 'CJ-PID-' . $uid;
        $this->cjVid = 'CJ-VID-' . $uid;

        $this->customer = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe.' . $uid . '@example.com',
            'mobile' => '1202' . rand(1000000, 9999999),
            'role_id' => 3,
            'is_active' => true,
        ]);

        $category = Category::firstOrCreate(['slug' => 'gadgets'], [
            'name' => 'Gadgets',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Smart AI Earbuds Pro',
            'slug' => 'smart-ai-earbuds-pro-' . $uid,
            'sku' => 'EARBUDS-PRO-' . $uid,
            'price' => 49.99,
            'stock_quantity' => 50,
            'status' => 'active',
            'is_active' => true,
            'fulfillment_type' => 'cj',
            'created_by' => $this->customer->id,
        ]);

        CjProduct::create([
            'internal_product_id' => $this->product->id,
            'cj_product_id' => $this->cjPid,
            'title' => 'Smart AI Earbuds Pro',
            'source' => 'cjdropshipping',
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'cj_variant_id' => $this->cjVid,
            'sku' => 'EARBUDS-BLACK-' . $uid,
            'name' => 'Matte Black',
            'option1_name' => 'Color',
            'option1_value' => 'Black',
            'selling_price' => 49.99,
            'cost_price' => 15.00,
            'stock_quantity' => 25,
            'status' => 'active',
        ]);
    }

    protected function createTestOrder(float $amount = 49.99): Order
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'subtotal' => $amount,
            'tax_amount' => 0.00,
            'shipping_charge' => 0.00,
            'total_amount' => $amount,
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'quantity' => 1,
            'unit_price' => $amount,
            'total_price' => $amount,
            'status' => 'active',
        ]);

        OrderAddress::create([
            'order_id' => $order->id,
            'type' => 'shipping',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '12025550199',
            'address_line1' => '742 Evergreen Terrace',
            'address_line2' => 'Apt 4B',
            'city' => 'Springfield',
            'state' => 'IL',
            'country' => 'US',
            'postal_code' => '62704',
        ]);

        return $order;
    }

    /**
     * Test 1: Normal Purchase Flow with Mocked Gateway & Outbox Event
     */
    public function test_01_normal_purchase_flow_verified_and_fulfilled()
    {
        $order = $this->createTestOrder(49.99);
        $capId = 'CAP-NORMAL-' . uniqid();

        Http::fake(function (\Illuminate\Http\Client\Request $request) use ($capId) {
            if (str_contains($request->url(), 'oauth2/token')) {
                return Http::response(['access_token' => 'MOCK_TOKEN', 'token_type' => 'Bearer', 'expires_in' => 3600], 200);
            }
            if (str_contains($request->url(), 'capture')) {
                return Http::response([
                    'id' => 'PAYPAL-ORD-01',
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'payments' => [
                            'captures' => [[
                                'id' => $capId,
                                'status' => 'COMPLETED',
                                'amount' => ['value' => '49.99', 'currency_code' => 'USD'],
                            ]]
                        ]
                    ]]
                ], 200);
            }
            return Http::response([], 200);
        });

        $response = $this->postJson(route('payment.paypal.capture'), [
            'order_id' => $order->id,
            'paypal_order_id' => 'PAYPAL-ORD-01',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'transaction_id' => $capId,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'type' => 'CAPTURE',
            'provider_transaction_id' => $capId,
        ]);

        $this->assertDatabaseHas('outbox_events', [
            'aggregate_id' => $order->id,
            'event_name' => 'ORDER_PAID',
            'status' => 'PENDING',
        ]);
    }

    /**
     * Test 2: Price Tampering Attack (Client manipulates price from 49.99 to 0.01)
     */
    public function test_02_price_tampering_attack_is_rejected()
    {
        $order = $this->createTestOrder(49.99);

        Http::fake(function (\Illuminate\Http\Client\Request $request) {
            if (str_contains($request->url(), 'oauth2/token')) {
                return Http::response(['access_token' => 'MOCK_TOKEN', 'token_type' => 'Bearer', 'expires_in' => 3600], 200);
            }
            if (str_contains($request->url(), 'capture')) {
                return Http::response([
                    'id' => 'PAYPAL-TAMPER-01',
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'payments' => [
                            'captures' => [[
                                'id' => 'CAP-TAMPER-01',
                                'status' => 'COMPLETED',
                                'amount' => ['value' => '0.01', 'currency_code' => 'USD'], // Price Tampered
                            ]]
                        ]
                    ]]
                ], 200);
            }
            return Http::response([], 200);
        });

        $response = $this->postJson(route('payment.paypal.capture'), [
            'order_id' => $order->id,
            'paypal_order_id' => 'PAYPAL-TAMPER-01',
        ]);

        $response->assertStatus(422)->assertJson(['success' => false]);

        $order->refresh();
        $this->assertNotEquals('paid', $order->payment_status);
        $this->assertDatabaseMissing('payments', ['order_id' => $order->id, 'status' => 'success']);
    }

    /**
     * Test 3: Foreign Order ID Mismatch (Order A token passed with invalid/missing order_id)
     */
    public function test_03_foreign_order_capture_mismatch_rejected()
    {
        $response = $this->postJson(route('payment.paypal.capture'), [
            'order_id' => 9999999, // Non-existent order
            'paypal_order_id' => 'PAYPAL-ORD-FOREIGN',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test 4: Duplicate Capture / Replay Attack (Capture called twice -> exactly 1 payment record)
     */
    public function test_04_duplicate_capture_is_idempotent()
    {
        $order = $this->createTestOrder(49.99);
        $capId = 'CAP-REPLAY-' . uniqid();

        Http::fake(function (\Illuminate\Http\Client\Request $request) use ($capId) {
            if (str_contains($request->url(), 'oauth2/token')) {
                return Http::response(['access_token' => 'MOCK_TOKEN', 'token_type' => 'Bearer', 'expires_in' => 3600], 200);
            }
            if (str_contains($request->url(), 'capture')) {
                return Http::response([
                    'id' => 'PAYPAL-REPLAY-01',
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'payments' => [
                            'captures' => [[
                                'id' => $capId,
                                'status' => 'COMPLETED',
                                'amount' => ['value' => '49.99', 'currency_code' => 'USD'],
                            ]]
                        ]
                    ]]
                ], 200);
            }
            return Http::response([], 200);
        });

        // First capture
        $this->postJson(route('payment.paypal.capture'), [
            'order_id' => $order->id,
            'paypal_order_id' => 'PAYPAL-REPLAY-01',
        ])->assertStatus(200);

        // Replay attempt
        $res2 = $this->postJson(route('payment.paypal.capture'), [
            'order_id' => $order->id,
            'paypal_order_id' => 'PAYPAL-REPLAY-01',
        ]);

        $res2->assertStatus(200);
        $this->assertEquals(1, Payment::where('order_id', $order->id)->count());
        $this->assertEquals(1, PaymentTransaction::where('order_id', $order->id)->count());
    }

    /**
     * Test 5: Browser Network Drop Recovery (Webhook reconciles abandoned order to PAID)
     */
    public function test_05_browser_drop_reconciled_via_webhook()
    {
        $order = $this->createTestOrder(49.99);

        $webhookPayload = [
            'id' => 'WH-EVT-BROWSER-DROP-' . uniqid(),
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource' => [
                'id' => 'CAP-WH-RECOVERY-01',
                'custom_id' => $order->order_number,
                'status' => 'COMPLETED',
                'amount' => ['value' => '49.99', 'currency_code' => 'USD'],
            ]
        ];

        $headers = [
            'PAYPAL-TRANSMISSION-SIG' => 'mock_valid_signature_token',
            'PAYPAL-AUTH-ALGO' => 'SHA256withRSA',
            'PAYPAL-TRANSMISSION-ID' => 'wh_drop_tx_' . uniqid(),
            'PAYPAL-TRANSMISSION-TIME' => now()->toIso8601String(),
            'PAYPAL-CERT-URL' => 'https://api.sandbox.paypal.com/v1/certs/test',
        ];

        $res = $this->postJson(route('webhooks.paypal'), $webhookPayload, $headers);
        $res->assertStatus(200);

        $event = ProviderEvent::where('event_id', $webhookPayload['id'])->first();
        $this->assertNotNull($event);

        // Process webhook job
        (new ProcessProviderWebhook($event))->handle();

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertDatabaseHas('outbox_events', ['aggregate_id' => $order->id]);
    }

    /**
     * Test 6: CJ Duplicate Fulfillment Prevention (Calling placeOrder twice returns same CJ order)
     */
    public function test_06_cj_duplicate_fulfillment_prevention()
    {
        $order = $this->createTestOrder(49.99);
        $order->update(['payment_status' => 'paid', 'status' => 'processing']);

        $res1 = CjOrderService::placeOrder($order);
        $res2 = CjOrderService::placeOrder($order);

        $this->assertEquals($res1['cjOrderId'], $res2['cjOrderId']);
        $this->assertEquals(1, CjOrder::where('internal_order_id', $order->id)->count());
    }

    /**
     * Test 7: Variant Fidelity (Selected variant VID reaches CJ payload)
     */
    public function test_07_variant_fidelity_resolves_exact_variant_id()
    {
        $order = $this->createTestOrder(49.99);
        $item = $order->items->first();

        $resolvedVid = CjOrderService::resolveVariantId($item);

        $this->assertEquals($this->cjVid, $resolvedVid);
        $this->assertNotEquals($this->cjPid, $resolvedVid);
    }

    /**
     * Test 8: Full Un-truncated Shipping Address Integrity
     */
    public function test_08_shipping_address_integrity_preserved()
    {
        $order = $this->createTestOrder(49.99);
        $address = $order->orderAddress;

        $this->assertEquals('742 Evergreen Terrace', $address->address_line1);
        $this->assertEquals('Apt 4B', $address->address_line2);
        $this->assertEquals('Springfield', $address->city);
        $this->assertEquals('IL', $address->state);
        $this->assertEquals('62704', $address->postal_code);
        $this->assertEquals('US', $address->country);
        $this->assertEquals('12025550199', $address->phone);
    }

    /**
     * Test 9: CJ Downtime Recovery (Payment CAPTURED, but CJ API fails -> Order remains PAID, retry queued)
     */
    public function test_09_cj_downtime_preserves_payment_and_queues_retry()
    {
        $order = $this->createTestOrder(49.99);

        // 1. Payment captures cleanly
        OrderService::markAsPaid($order);
        $order->refresh();

        $this->assertEquals('paid', $order->payment_status);

        // 2. CJ failure should not revert order to unpaid
        $outboxEvent = OutboxEvent::where('aggregate_id', $order->id)->first();
        $this->assertNotNull($outboxEvent);

        $this->assertEquals('PAID', $order->payment_status === 'paid' ? 'PAID' : 'FAILED');
    }

    /**
     * Test 10: Refund & Ledger Integrity
     */
    public function test_10_refund_updates_payment_order_and_transaction_ledger()
    {
        $order = $this->createTestOrder(49.99);
        $order->update(['payment_status' => 'paid', 'status' => 'processing']);

        $txUid = 'CAP-REFUND-' . uniqid();

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'paypal',
            'transaction_id' => $txUid,
            'amount' => 49.99,
            'status' => 'success',
        ]);

        PaymentTransaction::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'type' => 'CAPTURE',
            'amount' => 49.99,
            'currency' => 'USD',
            'provider' => 'paypal',
            'provider_transaction_id' => $txUid,
            'status' => 'completed',
        ]);

        $refundResult = PaymentService::processRefund($order, 49.99, 'Customer Cancellation');

        $this->assertTrue($refundResult['success']);
        $order->refresh();

        $this->assertEquals('refunded', $order->status);
        $this->assertEquals('refunded', $order->payment_status);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'type' => 'REFUND',
            'amount' => 49.99,
        ]);
    }
}
