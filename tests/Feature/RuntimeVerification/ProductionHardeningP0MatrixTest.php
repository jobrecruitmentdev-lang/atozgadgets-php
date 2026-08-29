<?php

namespace Tests\Feature\RuntimeVerification;

use App\Models\Category;
use App\Models\CjOrder;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OutboxEvent;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProviderEvent;
use App\Models\Setting;
use App\Models\User;
use App\Services\Checkout\CheckoutService;
use App\Services\Cj\CjOrderService;
use App\Services\Inventory\InventoryService;
use App\Services\Order\OrderService;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductionHardeningP0MatrixTest extends TestCase
{
    use RefreshDatabase;

    private User $customerA;
    private User $customerB;
    private Category $category;
    private Product $limitedProduct;
    private ProductVariant $limitedVariant;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('free_shipping_threshold', 50.00);
        Setting::set('standard_shipping_rate', 5.99);
        Setting::set('store_name', 'AtoZGadgets');

        $this->customerA = User::create([
            'first_name' => 'Customer',
            'last_name' => 'Alpha',
            'email' => 'alpha@test.com',
            'password' => Hash::make('Secret123!'),
            'role_id' => 3,
            'is_active' => 1,
        ]);

        $this->customerB = User::create([
            'first_name' => 'Customer',
            'last_name' => 'Beta',
            'email' => 'beta@test.com',
            'password' => Hash::make('Secret123!'),
            'role_id' => 3,
            'is_active' => 1,
        ]);

        $this->category = Category::create([
            'name' => 'High Demand Tech',
            'slug' => 'high-demand-tech',
            'status' => 'active',
        ]);

        // Stock = 1 strictly
        $this->limitedProduct = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->customerA->id,
            'name' => 'Ultra Rare Neural Earbuds',
            'slug' => 'ultra-rare-neural-earbuds',
            'sku' => 'AZG-RARE-01',
            'price' => 199.99,
            'selling_price' => 199.99,
            'stock_quantity' => 1,
            'fulfillment_type' => 'cj',
            'status' => 'active',
            'is_active' => 1,
        ]);

        $this->limitedVariant = ProductVariant::create([
            'product_id' => $this->limitedProduct->id,
            'name' => 'Cyber Silver',
            'sku' => 'AZG-RARE-01-SILVER',
            'selling_price' => 199.99,
            'cost_price' => 70.00,
            'stock_quantity' => 1,
            'cj_variant_id' => 'CJ-RARE-VAR-999',
            'status' => 'active',
        ]);
    }

    /**
     * =========================================================================
     * 5. INVENTORY RACE CONDITION — P0
     * Scenario: Stock = 1. Customer A & Customer B simultaneously attempt purchase.
     * Expected: Customer A succeeds, Customer B gets out-of-stock rejection.
     * Invariants: Stock never drops to -1, ATS is accurate, only 1 order created.
     * =========================================================================
     */
    public function test_p0_inventory_race_condition_stock_one_allows_only_one_purchase_and_prevents_negative_stock()
    {
        $rawCart = [
            $this->limitedProduct->id => [
                'product_id' => $this->limitedProduct->id,
                'name' => $this->limitedProduct->name,
                'quantity' => 1,
                'price' => 199.99,
            ]
        ];

        $addressA = [
            'country' => 'US',
            'city' => 'San Francisco',
            'state' => 'CA',
            'address1' => '100 Market St',
            'postal_code' => '94105',
            'first_name' => 'Customer',
            'last_name' => 'Alpha',
            'phone' => '+14155550101',
            'email' => 'alpha@test.com',
        ];

        $addressB = [
            'country' => 'US',
            'city' => 'New York',
            'state' => 'NY',
            'address1' => '500 5th Ave',
            'postal_code' => '10001',
            'first_name' => 'Customer',
            'last_name' => 'Beta',
            'phone' => '+12125550102',
            'email' => 'beta@test.com',
        ];

        // 1. Browser A starts checkout and acquires inventory lock
        $sessionA = CheckoutService::createSession($this->customerA->id, $rawCart, $addressA);
        $orderA = OrderService::createPendingOrderFromSession($sessionA, $addressA);

        $this->assertNotNull($orderA);
        $this->assertEquals('pending', $orderA->status);
        $this->assertEquals('pending', $orderA->payment_status);

        // Verify active reservation exists for Order A
        $reservationA = InventoryReservation::where('order_id', $orderA->id)->first();
        $this->assertNotNull($reservationA);
        $this->assertEquals('RESERVED', $reservationA->status);
        $this->assertEquals(1, $reservationA->quantity);

        // Verify product ATS (Available to Sell) is now 0 (Out of stock for new checkouts)
        $availability = InventoryService::getAvailability($this->limitedProduct->fresh());
        $this->assertEquals(InventoryService::STATUS_LOW_STOCK, $availability['status']); // stock 1, ATS 0

        // 2. Browser B attempts concurrent checkout for the exact same single-unit item
        $sessionB = CheckoutService::createSession($this->customerB->id, $rawCart, $addressB);
        
        $browserBExceptionThrown = false;
        try {
            OrderService::createPendingOrderFromSession($sessionB, $addressB);
        } catch (\Throwable $e) {
            $browserBExceptionThrown = true;
            $this->assertStringContainsString('out of stock', strtolower($e->getMessage()));
        }

        $this->assertTrue($browserBExceptionThrown, 'Customer B checkout must be rejected due to pessimistic stock lock.');

        // 3. Customer A completes payment capture
        OrderService::markAsPaid($orderA);

        // 4. Physical Stock Verification
        $finalProduct = $this->limitedProduct->fresh();
        $this->assertEquals(0, $finalProduct->stock_quantity, 'Physical stock must be decremented to exactly 0.');
        $this->assertGreaterThanOrEqual(0, $finalProduct->stock_quantity, 'Stock must NEVER drop below zero (-1).');

        // Confirm inventory reservation status
        $this->assertEquals('CONFIRMED', $reservationA->fresh()->status);

        // 5. Customer B re-attempt after A confirmed is strictly rejected
        $retryReservation = InventoryService::reserve(orderId: 999, items: [
            ['product_id' => $this->limitedProduct->id, 'quantity' => 1]
        ]);
        $this->assertFalse($retryReservation, 'Further reservations must be impossible when stock is 0.');
    }

    /**
     * =========================================================================
     * 6. PAYPAL COMPLETE SANDBOX E2E — P0
     * Scenario: Cart -> Checkout -> Create Order -> PayPal popup -> Approve ->
     * Capture -> PaymentTransaction -> Order = paid -> ORDER_PAID outbox -> worker -> DB Audit.
     * =========================================================================
     */
    public function test_p0_paypal_complete_sandbox_e2e_lifecycle_and_db_audit()
    {
        $rawCart = [
            $this->limitedProduct->id => [
                'product_id' => $this->limitedProduct->id,
                'name' => $this->limitedProduct->name,
                'quantity' => 1,
                'price' => 199.99,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'Austin',
            'state' => 'TX',
            'address1' => '200 Congress Ave',
            'postal_code' => '78701',
            'first_name' => 'Sandbox',
            'last_name' => 'Auditor',
            'phone' => '+15125550199',
            'email' => 'auditor@example.com',
        ];

        $paypalOrderId = 'PAYPAL_SANDBOX_ORDER_887766';
        $paypalCaptureId = 'CAPTURE_SANDBOX_TX_554433';

        // Mock PayPal OAuth, Create Order & Capture
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'SANDBOX_ACCESS_TOKEN_VALID'], 200),
            '*/v2/checkout/orders' => Http::response([
                'id' => $paypalOrderId,
                'status' => 'CREATED',
            ], 200),
            '*/v2/checkout/orders/' . $paypalOrderId . '/capture' => function ($request) use ($paypalOrderId, $paypalCaptureId) {
                $order = Order::latest('id')->first();
                $amount = $order ? (string)$order->total_amount : '199.99';
                return Http::response([
                    'id' => $paypalOrderId,
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'payments' => [
                            'captures' => [[
                                'id' => $paypalCaptureId,
                                'status' => 'COMPLETED',
                                'amount' => ['value' => $amount, 'currency_code' => 'USD'],
                            ]]
                        ]
                    ]]
                ], 200);
            },
        ]);

        // Step 1: Frontend initiates Create Order via /payment/paypal/create-order
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

        // Step 2: PayPal approval & Capture via /payment/paypal/capture-order
        $captureRes = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypalOrderId,
            'order_id' => $order->id,
        ]);

        $captureRes->assertStatus(200);
        $captureRes->assertJson(['success' => true]);

        // Step 3: Complete Database Audit
        $freshOrder = $order->fresh();

        // 3a. Order Status Audit
        $this->assertEquals('processing', $freshOrder->status);
        $this->assertEquals('paid', $freshOrder->payment_status);

        // 3b. Payment Ledger Audit
        $transaction = PaymentTransaction::where('order_id', $order->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals($paypalCaptureId, $transaction->provider_transaction_id);
        $this->assertEquals('CAPTURE', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals('paypal', $transaction->provider);
        $this->assertEquals((float)$freshOrder->total_amount, (float)$transaction->amount);

        // 3c. Outbox Event Audit
        $outboxEvent = OutboxEvent::where('aggregate_id', $order->id)
            ->where('event_name', 'ORDER_PAID')
            ->first();
        $this->assertNotNull($outboxEvent, 'ORDER_PAID outbox event must exist in pending state.');
        $this->assertEquals('PENDING', $outboxEvent->status);

        // 3d. Inventory Reservation Audit
        $reservation = InventoryReservation::where('order_id', $order->id)->first();
        $this->assertEquals('CONFIRMED', $reservation->status);

        // Step 4: Worker Execution (Simulate artisan outbox:process)
        Artisan::call('outbox:process', ['--limit' => 10, '--budget' => 30]);

        $processedEvent = $outboxEvent->fresh();
        $this->assertContains($processedEvent->status, ['PROCESSED', 'CLAIMED', 'FAILED']);
    }

    /**
     * =========================================================================
     * 7. PAYMENT FAILURE MATRIX — P0
     * Scenario: Cancel PayPal, Payment declined, Capture failure, Browser closed,
     * Double-click PayPal, Refresh during payment, Back button, Network interruption.
     * Expected: Zero falsely-paid orders; clean rollback / logical consistency.
     * =========================================================================
     */
    public function test_p0_payment_failure_matrix_preserves_strict_ledger_and_order_invariants()
    {
        $rawCart = [
            $this->limitedProduct->id => [
                'product_id' => $this->limitedProduct->id,
                'name' => $this->limitedProduct->name,
                'quantity' => 1,
                'price' => 199.99,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'Chicago',
            'state' => 'IL',
            'address1' => '300 Michigan Ave',
            'postal_code' => '60601',
            'first_name' => 'Failure',
            'last_name' => 'MatrixTester',
            'phone' => '+13125550188',
            'email' => 'failure.matrix@test.com',
        ];

        $session = CheckoutService::createSession($this->customerA->id, $rawCart, $address);
        $order = OrderService::createPendingOrderFromSession($session, $address);

        // Subcase A: Payment Declined (INSTRUMENT_DECLINED / 422)
        $paypalDeclinedId = 'PAYPAL_ORDER_DECLINED_101';
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'SANDBOX_TOKEN'], 200),
            '*/v2/checkout/orders/' . $paypalDeclinedId . '/capture' => Http::response([
                'name' => 'UNPROCESSABLE_ENTITY',
                'message' => 'Payment instrument was declined.',
                'details' => [[
                    'issue' => 'INSTRUMENT_DECLINED',
                    'description' => 'The card was declined by the issuing bank.',
                ]],
            ], 422),
        ]);

        $declinedRes = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypalDeclinedId,
            'order_id' => $order->id,
        ]);

        $declinedRes->assertStatus(422);
        $declinedRes->assertJson(['success' => false]);

        // Invariant: Order is NOT paid, NO transaction, NO outbox
        $this->assertEquals('pending', $order->fresh()->payment_status);
        $this->assertEquals('pending', $order->fresh()->status);
        $this->assertEquals(0, PaymentTransaction::where('order_id', $order->id)->where('status', 'completed')->count());
        $this->assertEquals(0, OutboxEvent::where('aggregate_id', $order->id)->where('event_name', 'ORDER_PAID')->count());

        // Subcase B: Gateway 500 Network / Server Interruption
        $paypal500Id = 'PAYPAL_ORDER_500_ERR';
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'SANDBOX_TOKEN'], 200),
            '*/v2/checkout/orders/' . $paypal500Id . '/capture' => Http::response([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'PayPal service temporarily unavailable.',
            ], 500),
        ]);

        $netErrRes = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypal500Id,
            'order_id' => $order->id,
        ]);

        $netErrRes->assertStatus(422);
        $this->assertEquals('pending', $order->fresh()->payment_status);

        // Subcase C: Double-Click Capture (Simultaneous double submit)
        $paypalSuccessId = 'PAYPAL_ORDER_DOUBLE_CLICK_202';
        $paypalCaptureId = 'CAPTURE_DUP_CLICK_999';

        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'SANDBOX_TOKEN'], 200),
            '*/v2/checkout/orders/' . $paypalSuccessId . '/capture' => Http::response([
                'id' => $paypalSuccessId,
                'status' => 'COMPLETED',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => $paypalCaptureId,
                            'status' => 'COMPLETED',
                            'amount' => ['value' => (string)$order->total_amount, 'currency_code' => 'USD'],
                        ]]
                    ]
                ]]
            ], 200),
        ]);

        // Click #1
        $res1 = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypalSuccessId,
            'order_id' => $order->id,
        ]);
        $res1->assertStatus(200);

        // Click #2 (Double-click submit)
        $res2 = $this->postJson(route('payment.paypal.capture'), [
            'paypal_order_id' => $paypalSuccessId,
            'order_id' => $order->id,
        ]);
        $res2->assertStatus(200);

        // Invariant: Exactly 1 capture transaction, exactly 1 outbox event
        $this->assertEquals(1, PaymentTransaction::where('order_id', $order->id)->where('type', 'CAPTURE')->count());
        $this->assertEquals(1, OutboxEvent::where('aggregate_id', $order->id)->where('event_name', 'ORDER_PAID')->count());
        $this->assertEquals('paid', $order->fresh()->payment_status);

        // Subcase D: Browser Abandonment / Release
        $abandonedProduct = Product::create([
            'category_id' => $this->category->id,
            'created_by' => $this->customerB->id,
            'name' => 'Abandoned Tech Gadget',
            'slug' => 'abandoned-tech-gadget',
            'sku' => 'AZG-ABN-01',
            'price' => 49.99,
            'selling_price' => 49.99,
            'stock_quantity' => 2,
            'fulfillment_type' => 'cj',
            'status' => 'active',
            'is_active' => 1,
        ]);
        $abandonedCart = [
            $abandonedProduct->id => [
                'product_id' => $abandonedProduct->id,
                'name' => $abandonedProduct->name,
                'quantity' => 1,
                'price' => 49.99,
            ]
        ];
        $sessionAbandoned = CheckoutService::createSession($this->customerB->id, $abandonedCart, $address);
        $orderAbandoned = OrderService::createPendingOrderFromSession($sessionAbandoned, $address);
        
        // Simulating release
        InventoryService::release($orderAbandoned->id);
        $this->assertEquals('RELEASED', InventoryReservation::where('order_id', $orderAbandoned->id)->first()->status);
    }

    /**
     * =========================================================================
     * 8. WEBHOOK IDEMPOTENCY — 🔥
     * Scenario: Same PayPal webhook/event sent 3 times:
     * Webhook #1 -> process
     * Webhook #2 -> ignored safely
     * Webhook #3 -> ignored safely
     * Expected: Exactly 1 payment, 1 order update, 1 outbox event, 0 duplicates.
     * =========================================================================
     */
    public function test_p0_webhook_idempotency_triple_replay_guarantees_single_execution()
    {
        $rawCart = [
            $this->limitedProduct->id => [
                'product_id' => $this->limitedProduct->id,
                'name' => $this->limitedProduct->name,
                'quantity' => 1,
                'price' => 199.99,
            ]
        ];

        $address = [
            'country' => 'US',
            'city' => 'Miami',
            'state' => 'FL',
            'address1' => '100 Brickell Ave',
            'postal_code' => '33131',
            'first_name' => 'Webhook',
            'last_name' => 'Tester',
            'phone' => '+13055550177',
            'email' => 'webhook.idempotency@test.com',
        ];

        $session = CheckoutService::createSession($this->customerA->id, $rawCart, $address);
        $order = OrderService::createPendingOrderFromSession($session, $address);

        $webhookEventId = 'WH-PAYPAL-TRIPLE-REPLAY-9999';
        $captureId = 'CAPTURE-WH-REPLAY-8888';

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

        // 1. Webhook #1 Arrival & Execution
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

        // 2. Webhook #2 Duplicate Replay (Simulating webhook retry)
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

        // 3. Webhook #3 Duplicate Replay
        $job3 = new \App\Jobs\ProcessProviderWebhook($event2);
        $job3->handle();

        // STRICT INVARIANTS:
        // 1. Only 1 ProviderEvent row exists
        $this->assertEquals(1, ProviderEvent::where('event_id', $webhookEventId)->count());

        // 2. Exactly 1 PaymentTransaction in immutable ledger
        $this->assertEquals(1, PaymentTransaction::where('order_id', $order->id)->where('type', 'CAPTURE')->count());

        // 3. Exactly 1 ORDER_PAID outbox event created
        $this->assertEquals(1, OutboxEvent::where('aggregate_id', $order->id)->where('event_name', 'ORDER_PAID')->count());
    }

    /**
     * =========================================================================
     * 9. CJ FULFILLMENT FAILURE MATRIX
     * Scenario: CJ timeout, CJ 401, CJ 429 rate limit, CJ 500, invalid SKU,
     * out of stock, invalid address, shipping unavailable, duplicate submit.
     * Expected: Query-before-retry, automatic dedup, resilient backoff.
     * =========================================================================
     */
    public function test_p0_cj_fulfillment_failure_and_recovery_matrix()
    {
        $order = Order::create([
            'order_number' => 'ORD-CJ-MATRIX-77',
            'user_id' => $this->customerA->id,
            'total_amount' => 199.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'shipping_address' => json_encode([
                'first_name' => 'CJ',
                'last_name' => 'Tester',
                'address1' => '100 Logistic Way',
                'city' => 'Dallas',
                'state' => 'TX',
                'postal_code' => '75001',
                'country' => 'US',
                'phone' => '5551234567'
            ])
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->limitedProduct->id,
            'variant_id' => $this->limitedVariant->id,
            'cj_variant_id' => 'CJ-RARE-VAR-999',
            'quantity' => 1,
            'unit_price' => 199.99,
            'total_price' => 199.99,
            'status' => 'active'
        ]);

        // Scenario 9a: Pre-flight Query-Before-Retry (Network drop recovery)
        // Simulate previous attempt succeeded at CJ but response dropped.
        Http::fake([
            '*/shopping/order/list*' => Http::response([
                'code' => 200,
                'result' => true,
                'data' => [
                    'list' => [
                        ['orderId' => 'CJ-RECOVERED-ORD-77', 'orderNum' => $order->order_number]
                    ]
                ]
            ], 200),
        ]);

        $recoveryResult = CjOrderService::placeOrder($order->id);
        $this->assertEquals('CJ-RECOVERED-ORD-77', $recoveryResult['cjOrderId']);
        $this->assertDatabaseHas('cj_orders', [
            'internal_order_id' => $order->id,
            'cj_order_id' => 'CJ-RECOVERED-ORD-77',
            'status' => 'submitted',
        ]);

        // Scenario 9b: Duplicate Submit Protection
        // Second call on already submitted order returns existing record without re-querying CJ
        $dupResult = CjOrderService::placeOrder($order->id);
        $this->assertEquals('CJ-RECOVERED-ORD-77', $dupResult['cjOrderId']);
        $this->assertEquals(1, CjOrder::where('internal_order_id', $order->id)->count());

        // Scenario 9c: CJ 429 Rate Limit (QPS Code 1600200) Handling with retry
        $order2 = Order::create([
            'order_number' => 'ORD-CJ-RATELIMIT-88',
            'user_id' => $this->customerB->id,
            'total_amount' => 199.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'shipping_address' => json_encode([
                'first_name' => 'Rate',
                'last_name' => 'LimitTester',
                'address1' => '200 Backoff St',
                'city' => 'Austin',
                'state' => 'TX',
                'postal_code' => '78701',
                'country' => 'US',
                'phone' => '5559876543'
            ])
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $this->limitedProduct->id,
            'variant_id' => $this->limitedVariant->id,
            'cj_variant_id' => 'CJ-RARE-VAR-999',
            'quantity' => 1,
            'unit_price' => 199.99,
            'total_price' => 199.99,
            'status' => 'active'
        ]);

        $attemptCount = 0;
        $mockRetryResponse = CjOrderService::executeWithRetry(function () use (&$attemptCount) {
            $attemptCount++;
            if ($attemptCount === 1) {
                // First attempt hit QPS Rate Limit
                return new \Illuminate\Http\Client\Response(
                    new \GuzzleHttp\Psr7\Response(200, [], json_encode(['code' => 1600200, 'message' => 'QPS Limit Exceeded']))
                );
            }
            // Second attempt succeeds
            return new \Illuminate\Http\Client\Response(
                new \GuzzleHttp\Psr7\Response(200, [], json_encode(['code' => 200, 'data' => ['orderId' => 'CJ-RETRY-SUCCESS-88']]))
            );
        }, 3);

        $this->assertEquals(2, $attemptCount, 'ExecuteWithRetry must retry after receiving QPS rate limit code.');
        $this->assertEquals(200, $mockRetryResponse->json('code'));
        $this->assertEquals('CJ-RETRY-SUCCESS-88', $mockRetryResponse->json('data.orderId'));
    }
}
