<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OutboxEvent;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProviderEvent;
use App\Models\Setting;
use App\Models\User;
use App\Services\Checkout\CheckoutService;
use App\Services\Order\OrderService;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class PayPalE2EAndResilienceTest extends TestCase
{
    use RefreshDatabase;

    private $category;
    private $user;
    private $product;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('free_shipping_threshold', 50.00);
        Setting::set('standard_shipping_rate', 5.99);

        $this->category = Category::create([
            'name' => 'Flagship Gadgets',
            'slug' => 'flagship-gadgets',
        ]);

        $this->user = User::create([
            'first_name' => 'E2E',
            'last_name' => 'Customer',
            'email' => 'e2e.customer@example.com',
            'password' => Hash::make('Password123!'),
            'role_id' => 3,
            'is_active' => 1,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'name' => 'AtoZ Holographic Projector',
            'slug' => 'atoz-holographic-projector',
            'sku' => 'HOLO-PROJ-01',
            'price' => 129.99,
            'selling_price' => 129.99,
            'stock_quantity' => 10,
            'fulfillment_type' => 'cj',
            'status' => 'active',
        ]);
    }

    /**
     * P0 TEST 6: Complete PayPal E2E Flow (Cart -> Checkout -> Intent -> Capture -> Ledger -> Paid -> Outbox -> Worker)
     */
    public function test_complete_paypal_e2e_lifecycle_and_database_audit()
    {
        $rawCart = [
            $this->product->id => [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'quantity' => 1,
                'price' => 129.99,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'San Francisco',
            'state' => 'CA',
            'address1' => '100 Market St',
            'postal_code' => '94105',
            'first_name' => 'E2E',
            'last_name' => 'Customer',
            'phone' => '+14155550144',
            'email' => 'e2e.customer@example.com',
        ];

        $paypalOrderId = 'PAYPAL_ORDER_LIVE_12345';
        $paypalCaptureId = 'CAPTURE_TX_987654';

        // Mock PayPal OAuth & Capture Gateway Responses
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'VALID_SANDBOX_TOKEN'], 200),
            '*/v2/checkout/orders/' . $paypalOrderId . '/capture' => Http::response([
                'id' => $paypalOrderId,
                'status' => 'COMPLETED',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => $paypalCaptureId,
                            'status' => 'COMPLETED',
                            'amount' => ['value' => '138.44', 'currency_code' => 'USD'],
                        ]]
                    ]
                ]]
            ], 200),
            '*/v2/checkout/orders' => Http::response([
                'id' => $paypalOrderId,
                'status' => 'CREATED',
            ], 200),
        ]);

        // 1. Step 1: Create Order via /payment/paypal/create-order
        $createRes = $this->withSession(['cart' => $rawCart, 'checkout_shipping' => $address])
            ->postJson(route('payment.paypal.create'), [
                'address' => $address
            ]);

        $createRes->assertStatus(200);
        $orderId = $createRes->json('order_id');
        $this->assertNotNull($orderId);

        $order = Order::find($orderId);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('pending', $order->payment_status);

        // 2. Step 2: Capture Order via /payment/paypal/capture-order
        $captureRes = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypalOrderId,
            'order_id' => $order->id,
        ]);

        $captureRes->assertStatus(200);
        $captureRes->assertJson(['success' => true]);

        // 3. Step 3: Comprehensive Database Audit
        $refreshedOrder = $order->fresh();

        // 3a. Order Status Audit
        $this->assertEquals('processing', $refreshedOrder->status);
        $this->assertEquals('paid', $refreshedOrder->payment_status);

        // 3b. Payment Transaction Ledger Audit
        $transaction = PaymentTransaction::where('order_id', $order->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals($paypalCaptureId, $transaction->provider_transaction_id);
        $this->assertEquals('CAPTURE', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals('paypal', $transaction->provider);

        // 3c. Outbox Event Audit
        $outboxEvent = OutboxEvent::where('aggregate_id', $order->id)
            ->where('event_name', 'ORDER_PAID')
            ->first();
        $this->assertNotNull($outboxEvent, 'ORDER_PAID outbox event must be generated upon payment.');
        $this->assertEquals('PENDING', $outboxEvent->status);

        // 4. Step 4: Worker Execution (Simulate cron artisan outbox:process)
        Artisan::call('outbox:process', ['--limit' => 10, '--budget' => 30]);

        $processedEvent = $outboxEvent->fresh();
        $this->assertContains($processedEvent->status, ['PROCESSED', 'CLAIMED', 'FAILED']);
    }

    /**
     * P0 TEST 7: Payment Failure & Resilience Matrix (Zero Falsely-Paid Orders)
     */
    public function test_payment_failure_and_rejections_preserve_clean_state()
    {
        $rawCart = [
            $this->product->id => [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'quantity' => 1,
                'price' => 129.99,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'Austin',
            'state' => 'TX',
            'address1' => '500 Congress Ave',
            'postal_code' => '78701',
            'first_name' => 'Resilience',
            'last_name' => 'Tester',
            'phone' => '+15125550122',
            'email' => 'resilience@example.com',
        ];

        $session = CheckoutService::createSession($this->user->id, $rawCart, $address);
        $order = OrderService::createPendingOrderFromSession($session, $address);

        $paypalOrderId = 'PAYPAL_ORDER_DECLINED_456';

        // Mock PayPal Capture Failure (e.g. Card Declined / 422 Unprocessable)
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'VALID_SANDBOX_TOKEN'], 200),
            '*/v2/checkout/orders/' . $paypalOrderId . '/capture' => Http::response([
                'name' => 'UNPROCESSABLE_ENTITY',
                'message' => 'The requested action could not be performed, semantically incorrect, or failed business validation.',
                'details' => [[
                    'issue' => 'INSTRUMENT_DECLINED',
                    'description' => 'The instrument presented was declined by the processor.',
                ]],
            ], 422),
        ]);

        // Execute Capture
        $captureRes = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypalOrderId,
            'order_id' => $order->id,
        ]);

        $captureRes->assertStatus(422);
        $captureRes->assertJson(['success' => false]);

        // CRITICAL INVARIANTS:
        $freshOrder = $order->fresh();
        // 1. Order MUST NOT be marked paid
        $this->assertEquals('pending', $freshOrder->status);
        $this->assertEquals('pending', $freshOrder->payment_status);

        // 2. No successful CAPTURE transaction
        $this->assertDatabaseMissing('payment_transactions', [
            'order_id' => $order->id,
            'status' => 'completed',
        ]);

        // 3. No ORDER_PAID outbox event created
        $this->assertDatabaseMissing('outbox_events', [
            'aggregate_id' => $order->id,
            'event_name' => 'ORDER_PAID',
        ]);
    }

    /**
     * P0 TEST 7b: Double-Click / Duplicate Capture Idempotency Test
     */
    public function test_double_click_or_duplicate_capture_is_safely_idempotent()
    {
        $rawCart = [
            $this->product->id => [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'quantity' => 1,
                'price' => 129.99,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'Seattle',
            'state' => 'WA',
            'address1' => '400 Pine St',
            'postal_code' => '98101',
            'first_name' => 'Duplicate',
            'last_name' => 'Tester',
            'phone' => '+12065550133',
            'email' => 'duplicate@example.com',
        ];

        $session = CheckoutService::createSession($this->user->id, $rawCart, $address);
        $order = OrderService::createPendingOrderFromSession($session, $address);

        $paypalOrderId = 'PAYPAL_ORDER_DUP_789';
        $paypalCaptureId = 'CAP_DUP_12345';

        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'VALID_SANDBOX_TOKEN'], 200),
            '*/v2/checkout/orders/' . $paypalOrderId . '/capture' => Http::response([
                'id' => $paypalOrderId,
                'status' => 'COMPLETED',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => $paypalCaptureId,
                            'status' => 'COMPLETED',
                            'amount' => ['value' => '138.44', 'currency_code' => 'USD'],
                        ]]
                    ]
                ]]
            ], 200),
        ]);

        // Click #1
        $res1 = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypalOrderId,
            'order_id' => $order->id,
        ]);
        $res1->assertStatus(200);

        // Click #2 (Simultaneous or Duplicate Click)
        $res2 = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypalOrderId,
            'order_id' => $order->id,
        ]);
        $res2->assertStatus(200);

        // INVARIANT: Exactly 1 transaction recorded, no duplicate charge
        $txCount = PaymentTransaction::where('order_id', $order->id)
            ->where('type', 'CAPTURE')
            ->count();
        $this->assertEquals(1, $txCount, 'Duplicate clicks must not produce multiple payment transactions.');
    }

    /**
     * P0 TEST 8: Webhook Idempotency (Event #1 Processed, Event #2 & #3 Safely Ignored)
     */
    public function test_webhook_idempotency_prevents_duplicate_payments_and_fulfillments()
    {
        $rawCart = [
            $this->product->id => [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'quantity' => 1,
                'price' => 129.99,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'Miami',
            'state' => 'FL',
            'address1' => '200 Biscayne Blvd',
            'postal_code' => '33132',
            'first_name' => 'Webhook',
            'last_name' => 'Tester',
            'phone' => '+13055550111',
            'email' => 'webhook@example.com',
        ];

        $session = CheckoutService::createSession($this->user->id, $rawCart, $address);
        $order = OrderService::createPendingOrderFromSession($session, $address);

        $webhookEventId = 'WH-EVENT-ID-UNIQUE-9999';
        $captureId = 'CAPTURE-WH-0001';

        $webhookPayload = [
            'id' => $webhookEventId,
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource' => [
                'id' => $captureId,
                'custom_id' => $order->order_number,
                'amount' => [
                    'value' => (string)$order->total_amount,
                    'currency_code' => 'USD',
                ],
            ]
        ];

        // 1. Webhook #1 Execution (Simulating Job Process)
        $event1 = ProviderEvent::create([
            'event_id' => $webhookEventId,
            'provider' => 'paypal',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'payload' => $webhookPayload,
            'signature_verified' => true,
            'processing_status' => 'RECEIVED',
            'attempts' => 0,
        ]);

        $job1 = new \App\Jobs\ProcessProviderWebhook($event1);
        $job1->handle();

        $this->assertEquals('PROCESSED', $event1->fresh()->processing_status);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('processing', $order->fresh()->status);

        // 2. Webhook #2 Duplicate Execution (Same event ID)
        $event2 = ProviderEvent::firstOrCreate(
            ['event_id' => $webhookEventId],
            [
                'provider' => 'paypal',
                'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
                'payload' => $webhookPayload,
                'signature_verified' => true,
                'processing_status' => 'RECEIVED',
                'attempts' => 0,
            ]
        );

        $job2 = new \App\Jobs\ProcessProviderWebhook($event2);
        $job2->handle();

        // 3. Webhook #3 Duplicate Execution
        $job3 = new \App\Jobs\ProcessProviderWebhook($event2);
        $job3->handle();

        // INVARIANTS:
        // 1. Only 1 ProviderEvent row in database
        $this->assertEquals(1, ProviderEvent::where('event_id', $webhookEventId)->count());

        // 2. Only 1 PaymentTransaction in ledger
        $this->assertEquals(1, PaymentTransaction::where('order_id', $order->id)->where('type', 'CAPTURE')->count());

        // 3. Only 1 ORDER_PAID outbox event created
        $this->assertEquals(1, OutboxEvent::where('aggregate_id', $order->id)->where('event_name', 'ORDER_PAID')->count());
    }
}
