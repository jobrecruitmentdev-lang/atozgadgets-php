<?php

namespace Tests\Feature\CriticalPath;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\CjOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControlTowerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_tower_' . uniqid() . '@example.com',
            'mobile' => '1202' . rand(1000000, 9999999),
            'role_id' => 1,
            'is_active' => true,
        ]);

        $category = Category::firstOrCreate(['slug' => 'gadgets'], [
            'name' => 'Gadgets',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'AtoZ Smart Hub Ultra',
            'slug' => 'smart-hub-ultra-' . uniqid(),
            'sku' => 'HUB-' . strtoupper(uniqid()),
            'price' => 99.99,
            'stock_quantity' => 20,
            'status' => 'active',
            'is_active' => true,
            'fulfillment_type' => 'cj',
            'created_by' => $this->admin->id,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'cj_variant_id' => 'CJ-VID-TOWER-1',
            'sku' => 'HUB-BLACK-' . uniqid(),
            'name' => 'Black Edition',
            'selling_price' => 99.99,
            'cost_price' => 30.00,
            'stock_quantity' => 10,
            'status' => 'active',
        ]);

        $uid = uniqid();
        $this->order = Order::create([
            'user_id' => $this->admin->id,
            'order_number' => 'ORD-TOWER-' . $uid,
            'subtotal' => 99.99,
            'tax_amount' => 0.00,
            'shipping_charge' => 0.00,
            'total_amount' => 99.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'fulfillment_status' => 'submitted',
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'unit_price' => 99.99,
            'total_price' => 99.99,
            'status' => 'active',
        ]);

        OrderAddress::create([
            'order_id' => $this->order->id,
            'type' => 'shipping',
            'first_name' => 'Sarah',
            'last_name' => 'Connor',
            'email' => 'sarah@example.com',
            'phone' => '12025550188',
            'address_line1' => '123 Tech Boulevard',
            'city' => 'Austin',
            'state' => 'TX',
            'country' => 'US',
            'postal_code' => '78701',
        ]);

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'payment_method' => 'paypal',
            'transaction_id' => 'CAP-TOWER-' . $uid,
            'amount' => 99.99,
            'status' => 'success',
        ]);

        PaymentTransaction::create([
            'order_id' => $this->order->id,
            'payment_id' => $payment->id,
            'type' => 'CAPTURE',
            'amount' => 99.99,
            'currency' => 'USD',
            'provider' => 'paypal',
            'provider_transaction_id' => 'CAP-TOWER-' . $uid,
            'status' => 'completed',
        ]);

        CjOrder::create([
            'internal_order_id' => $this->order->id,
            'cj_order_id' => 'CJ-ORDER-' . $uid,
            'status' => 'submitted',
            'order_amount' => 99.99,
            'shipping_fee' => 5.00,
            'tracking_number' => 'CJTRACK' . rand(1000000, 9999999) . 'US',
            'logistic_name' => 'CJPacket Fast Line',
        ]);
    }

    public function test_admin_control_tower_renders_with_all_cards()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.show', $this->order->id));

        $response->assertStatus(200);
        $response->assertSeeText($this->order->order_number);
        $response->assertSeeText('Customer');
        $response->assertSeeText('Verified Address');
        $response->assertSeeText('Payment');
        $response->assertSeeText('Financial Ledger');
        $response->assertSeeText('Line Items');
        $response->assertSeeText('Variant Fidelity');
        $response->assertSeeText('CJ Dropshipping');
        $response->assertSeeText('Tracking');
        $response->assertSeeText('Step-by-Step Audit Timeline');
        $response->assertSeeText('Sarah Connor');
        $response->assertSeeText('CJ-VID-TOWER-1');
    }
}
