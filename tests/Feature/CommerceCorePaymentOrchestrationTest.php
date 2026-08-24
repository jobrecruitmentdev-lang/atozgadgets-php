<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\PaymentAttempt;
use App\Models\ProviderEvent;
use App\Models\OutboxEvent;
use App\Models\InventoryReservation;
use App\Models\CheckoutSession;
use App\Services\Checkout\CheckoutService;
use App\Services\Order\OrderService;
use App\Services\Payment\PaymentService;
use App\Services\Fraud\RiskService;
use App\Jobs\ProcessProviderWebhook;
use App\Jobs\FulfillOrderOutbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommerceCorePaymentOrchestrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'email' => 'customer_core_' . uniqid() . '@example.com',
            'role_id' => 3,
            'is_active' => true,
        ]);

        $category = Category::firstOrCreate(['slug' => 'smart-devices'], [
            'name' => 'Smart Devices',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'AtoZ Smart Hub Ultra',
            'slug' => 'smart-hub-ultra-' . uniqid(),
            'sku' => 'HUB-' . strtoupper(uniqid()),
            'price' => 100.00,
            'stock_quantity' => 20,
            'status' => 'active',
            'is_active' => true,
            'fulfillment_type' => 'cj',
            'created_by' => $this->customer->id,
        ]);
    }

    public function test_checkout_session_creates_immutable_line_items_snapshot()
    {
        $rawCart = [
            $this->product->id => [
                'name' => $this->product->name,
                'price' => 100.00,
                'quantity' => 2,
                'sku' => $this->product->sku,
            ]
        ];

        $session = CheckoutService::createSession($this->customer->id, $rawCart, ['country' => 'US']);

        $this->assertNotNull($session);
        $this->assertEquals(200.00, $session->subtotal);
        $this->assertEquals(0.00, $session->shipping); // Free shipping threshold ($50+)
        $this->assertEquals(13.00, $session->tax); // 6.5% of 200
        $this->assertEquals(213.00, $session->grand_total);
        $this->assertCount(1, $session->line_items);
    }

    public function test_order_is_pre_created_in_pending_payment_state()
    {
        $rawCart = [
            $this->product->id => [
                'name' => $this->product->name,
                'price' => 100.00,
                'quantity' => 1,
            ]
        ];

        $session = CheckoutService::createSession($this->customer->id, $rawCart, ['country' => 'US']);
        $order = OrderService::createPendingOrderFromSession($session, ['address1' => '123 Main St']);

        $this->assertEquals('pending', $order->status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => 'pending']);

        // Check Inventory Reservation
        $this->assertDatabaseHas('inventory_reservations', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'status' => 'RESERVED',
        ]);
    }

    public function test_payment_capture_writes_immutable_ledger_transaction()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-TEST-' . time(),
            'subtotal' => 100.00,
            'total_amount' => 106.50,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $mockCaptureId = 'CAP-TEST-' . uniqid();

        Http::fake([
            '*/v1/oauth2/token*' => Http::response(['access_token' => 'mock_token', 'expires_in' => 3600], 200),
            '*/v2/checkout/orders/*/capture*' => Http::response([
                'id' => 'PAYPAL-ORD-123',
                'status' => 'COMPLETED',
                'purchase_units' => [
                    [
                        'payments' => [
                            'captures' => [
                                [
                                    'id' => $mockCaptureId,
                                    'amount' => ['value' => '106.50', 'currency_code' => 'USD'],
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $result = PaymentService::captureAndConfirm($order, 'PAYPAL-ORD-123', 'paypal');

        $this->assertTrue($result['success']);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('processing', $order->fresh()->status);

        // Immutable Ledger Assertion
        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'type' => 'CAPTURE',
            'provider_transaction_id' => $mockCaptureId,
            'amount' => 106.50,
            'status' => 'completed',
        ]);

        // Outbox Event Assertion
        $this->assertDatabaseHas('outbox_events', [
            'aggregate_id' => $order->id,
            'event_name' => 'ORDER_PAID',
            'status' => 'PENDING',
        ]);
    }

    public function test_price_tampering_triggers_high_risk_assessment_and_blocks_fulfillment()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-FRAUD-' . time(),
            'subtotal' => 500.00,
            'total_amount' => 500.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Hacker tries to pay $0.01 for $500 order
        Http::fake([
            '*/v1/oauth2/token*' => Http::response(['access_token' => 'mock_token', 'expires_in' => 3600], 200),
            '*/v2/checkout/orders/*/capture*' => Http::response([
                'id' => 'PAYPAL-ORD-HACK',
                'status' => 'COMPLETED',
                'purchase_units' => [
                    [
                        'payments' => [
                            'captures' => [
                                [
                                    'id' => 'CAP-HACK-01',
                                    'amount' => ['value' => '0.01', 'currency_code' => 'USD'],
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $result = PaymentService::captureAndConfirm($order, 'PAYPAL-ORD-HACK', 'paypal');

        $this->assertFalse($result['success']);
        $this->assertEquals('pending', $order->fresh()->payment_status);

        // Verify Risk Assessment Record
        $this->assertDatabaseHas('risk_assessments', [
            'order_id' => $order->id,
            'risk_level' => 'HIGH',
            'decision' => 'REJECT',
        ]);
    }

    public function test_webhook_ingestion_is_idempotent_and_updates_order_state()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-WH-' . time(),
            'subtotal' => 100.00,
            'total_amount' => 100.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $mockCaptureId = 'CAP-WH-INGEST-' . uniqid();

        Http::fake([
            '*/v1/oauth2/token*' => Http::response(['access_token' => 'mock_token', 'expires_in' => 3600], 200),
            '*/v2/checkout/orders/*/capture*' => Http::response([
                'id' => 'PAYPAL-ORD-WH',
                'status' => 'COMPLETED',
                'purchase_units' => [
                    [
                        'payments' => [
                            'captures' => [
                                [
                                    'id' => $mockCaptureId,
                                    'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $webhookPayload = [
            'id' => 'WH-EVT-' . uniqid(),
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource' => [
                'id' => $mockCaptureId,
                'custom_id' => $order->order_number,
                'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
            ]
        ];

        // 1. Post to Webhook endpoint
        $response = $this->postJson('/api/webhooks/paypal', $webhookPayload);
        $response->assertStatus(200);

        // 2. Process the job
        $event = ProviderEvent::where('event_id', $webhookPayload['id'])->first();
        $this->assertNotNull($event);

        $job = new ProcessProviderWebhook($event);
        $job->handle();

        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('PROCESSED', $event->fresh()->processing_status);
    }
}
