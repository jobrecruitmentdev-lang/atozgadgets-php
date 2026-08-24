<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\SupplierOrder;
use App\Models\OutboxEvent;
use App\Models\CheckoutSession;
use App\Services\Checkout\CheckoutService;
use App\Services\Order\OrderService;
use App\Services\PricingEngine;
use App\Services\Cj\CjSupplierAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommerceArchitectureV2Test extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\Setting::set('cj_sandbox_mode', '1');

        $this->user = User::factory()->create([
            'email' => 'arch_v2_' . uniqid() . '@example.com',
            'role_id' => 3,
            'is_active' => true,
        ]);

        $category = Category::firstOrCreate(['slug' => 'tech-gadgets'], [
            'name' => 'Tech Gadgets',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'AtoZ Smart Drone Pro',
            'slug' => 'smart-drone-pro-' . uniqid(),
            'sku' => 'DRONE-' . strtoupper(uniqid()),
            'price' => 150.00,
            'stock_quantity' => 10,
            'status' => 'active',
            'is_active' => true,
            'fulfillment_type' => 'cj',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_address_snapshot_permanently_attached_to_order()
    {
        $rawCart = [
            $this->product->id => [
                'name' => $this->product->name,
                'price' => 150.00,
                'quantity' => 1,
            ]
        ];

        $shippingAddress = [
            'first_name' => 'Alexander',
            'last_name' => 'Wright',
            'email' => 'alex@example.com',
            'phone' => '+1555019922',
            'address1' => '742 Evergreen Terrace',
            'city' => 'Springfield',
            'state' => 'OR',
            'country' => 'US',
            'postal_code' => '97477',
        ];

        $session = CheckoutService::createSession($this->user->id, $rawCart, $shippingAddress);
        $order = OrderService::createPendingOrderFromSession($session, $shippingAddress);

        $this->assertDatabaseHas('order_addresses', [
            'order_id' => $order->id,
            'first_name' => 'Alexander',
            'last_name' => 'Wright',
            'address_line1' => '742 Evergreen Terrace',
            'country' => 'US',
            'postal_code' => '97477',
        ]);

        // Verify Order model dynamic accessor resolves immutable address
        $address = $order->fresh()->address;
        $this->assertEquals('Alexander', $address->first_name);
        $this->assertEquals('742 Evergreen Terrace', $address->address_line1);
    }

    public function test_pricing_engine_calculates_and_guards_profit_margin()
    {
        $supplierCost = 30.00;
        $sellingPrice = PricingEngine::calculateSellingPrice($supplierCost, 2.5); // $75.00

        $this->assertEquals(75.00, $sellingPrice);

        $profitGuard = PricingEngine::validateProfitMargin($sellingPrice, $supplierCost, 5.99);

        $this->assertTrue($profitGuard['is_profitable']);
        $this->assertEquals(39.01, $profitGuard['gross_margin']);
        $this->assertEquals(52.0, $profitGuard['margin_percentage']);
    }

    public function test_outbox_command_processes_paid_order_to_cj_supplier()
    {
        \App\Models\Setting::set('cj_sandbox_mode', '1');

        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-OUTBOX-' . uniqid(),
            'subtotal' => 150.00,
            'total_amount' => 150.00,
            'status' => 'processing',
            'payment_status' => 'paid',
            'shipping_address' => json_encode([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'address1' => '100 Broadway',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'US',
                'postal_code' => '10001',
                'phone' => '5551234567',
            ]),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 150.00,
            'total_price' => 150.00,
            'status' => 'active',
        ]);

        $outboxEvent = OutboxEvent::create([
            'event_name' => 'ORDER_PAID',
            'aggregate_type' => 'Order',
            'aggregate_id' => $order->id,
            'payload' => ['order_id' => $order->id],
            'status' => 'PENDING',
            'attempts' => 0,
        ]);

        // Run Outbox processor command
        Artisan::call('outbox:process', ['--limit' => 5]);

        $freshEvent = OutboxEvent::find($outboxEvent->id);
        $this->assertNotNull($freshEvent);
        $this->assertEquals('PROCESSED', $freshEvent->status, 'Event error: ' . ($freshEvent->error_message ?? 'none'));
        $this->assertDatabaseHas('supplier_orders', [
            'order_id' => $order->id,
            'supplier' => 'cj',
            'status' => 'submitted',
        ]);
    }
}
