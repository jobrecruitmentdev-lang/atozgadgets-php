<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\CjProduct;
use App\Models\CjOrder;
use App\Services\Cj\CjAddressNormalizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminCjFulfillmentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $customer;
    protected Product $cjProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'email' => 'admin_flow_' . uniqid() . '@atozgadgets.com',
            'role_id' => 1,
            'is_active' => true,
        ]);

        $this->customer = User::factory()->create([
            'email' => 'customer_flow_' . uniqid() . '@example.com',
            'role_id' => 3,
            'is_active' => true,
        ]);

        $category = Category::firstOrCreate(['slug' => 'smart-devices'], [
            'name' => 'Smart Devices',
            'status' => 'active',
        ]);

        $this->cjProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'AtoZ Smart Track Pro',
            'slug' => 'smart-track-pro-' . uniqid(),
            'sku' => 'CJ-TRACK-' . strtoupper(uniqid()),
            'price' => 59.99,
            'stock_quantity' => 50,
            'status' => 'active',
            'is_active' => true,
            'fulfillment_type' => 'cj',
            'created_by' => $this->superAdmin->id,
        ]);

        CjProduct::create([
            'internal_product_id' => $this->cjProduct->id,
            'cj_product_id' => 'CJ-PID-FLOW-' . uniqid(),
            'title' => 'AtoZ Smart Track Pro',
            'sell_price' => 20.00,
            'status' => 'imported',
        ]);
    }

    public function test_admin_orders_index_view_renders_cj_fulfillment_controls()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-VIEW-' . time(),
            'subtotal' => 59.99,
            'total_amount' => 59.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'shipping_address' => json_encode([
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'address1' => '10 Downing St',
                'city' => 'London',
                'postal_code' => 'SW1A 2AA',
                'country' => 'United Kingdom',
                'phone' => '+447911123456',
            ]),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->cjProduct->id,
            'quantity' => 1,
            'unit_price' => 59.99,
            'total_price' => 59.99,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)->get('/admin/orders');
        $response->assertStatus(200);
        $response->assertSee('Fulfill on CJ');
    }

    public function test_fulfill_with_cj_dispatches_paid_order_and_creates_cj_order_record()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-DISPATCH-' . time(),
            'subtotal' => 59.99,
            'total_amount' => 59.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'shipping_address' => json_encode([
                'first_name' => 'David',
                'last_name' => 'Miller',
                'address1' => '200 Bay Street',
                'city' => 'Toronto',
                'postal_code' => 'M5J 2J2',
                'country' => 'Canada',
                'phone' => '+14165550199',
            ]),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->cjProduct->id,
            'quantity' => 1,
            'unit_price' => 59.99,
            'total_price' => 59.99,
            'status' => 'active',
        ]);

        $mockCjId = 'CJ-FULFILL-TORONTO-' . uniqid();
        \App\Models\Setting::set('cj_sandbox_mode', '0');
        \Illuminate\Support\Facades\Cache::forget('setting_cj_sandbox_mode');

        Http::fake([
            '*/shopping/order/createOrderV2*' => Http::response([
                'code' => 200,
                'result' => true,
                'data' => ['orderId' => $mockCjId]
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

        $response = $this->actingAs($this->superAdmin)
                         ->post("/admin/orders/{$order->id}/fulfill-cj");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $cjOrder = CjOrder::where('internal_order_id', $order->id)->first();
        $this->assertNotNull($cjOrder);
        $this->assertEquals($mockCjId, $cjOrder->cj_order_id);
    }

    public function test_fulfill_with_cj_blocks_unpaid_order()
    {
        $unpaidOrder = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-UNPAID-' . time(),
            'subtotal' => 59.99,
            'total_amount' => 59.99,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)
                         ->post("/admin/orders/{$unpaidOrder->id}/fulfill-cj");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertNull(CjOrder::where('internal_order_id', $unpaidOrder->id)->first());
    }

    public function test_cj_address_normalizer_resolves_international_country_codes()
    {
        $this->assertEquals('GB', CjAddressNormalizer::normalizeCountryCode('United Kingdom'));
        $this->assertEquals('GB', CjAddressNormalizer::normalizeCountryCode('UK'));
        $this->assertEquals('CA', CjAddressNormalizer::normalizeCountryCode('Canada'));
        $this->assertEquals('AU', CjAddressNormalizer::normalizeCountryCode('Australia'));
        $this->assertEquals('DE', CjAddressNormalizer::normalizeCountryCode('Germany'));
        $this->assertEquals('IN', CjAddressNormalizer::normalizeCountryCode('India'));
        $this->assertEquals('US', CjAddressNormalizer::normalizeCountryCode('United States'));
        $this->assertEquals('AE', CjAddressNormalizer::normalizeCountryCode('UAE'));

        $this->assertEquals('+14165550199', CjAddressNormalizer::cleanPhone('+1 (416) 555-0199'));
    }
}
