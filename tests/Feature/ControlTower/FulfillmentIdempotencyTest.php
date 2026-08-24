<?php

namespace Tests\Feature\ControlTower;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderAddress;
use App\Models\FulfillmentAttempt;
use App\Services\Fulfillment\FulfillmentService;
use App\Services\Fulfillment\FulfillmentProviderInterface;
use App\Services\Fulfillment\FulfillmentResult;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FulfillmentIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_fulfillment_service_records_idempotent_attempts()
    {
        $user = User::factory()->create();

        $category = Category::firstOrCreate(['slug' => 'gadgets'], [
            'name' => 'Gadgets',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-IDEM-' . uniqid(),
            'total_amount' => 45.00,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Idempotent Test Widget',
            'slug' => 'idem-widget-' . uniqid(),
            'sku' => 'IDEM-WIDGET-' . uniqid(),
            'price' => 45.00,
            'stock_quantity' => 20,
            'fulfillment_type' => 'cj',
            'cj_product_id' => 'CJ-IDEM-P-1',
            'created_by' => $user->id,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 45.00,
            'total_price' => 45.00,
        ]);

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'transaction_id' => 'PAY-IDEM-' . uniqid(),
            'amount' => 45.00,
            'payment_method' => 'paypal',
            'status' => 'success',
        ]);

        OrderAddress::create([
            'order_id' => $order->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.idem@example.com',
            'phone' => '12025550199',
            'address_line1' => '456 Safe Lane',
            'city' => 'Austin',
            'state' => 'TX',
            'postal_code' => '78701',
            'country' => 'US',
        ]);

        // Mock the provider to return successful fulfillment result
        $mockProvider = $this->createMock(FulfillmentProviderInterface::class);
        $mockProvider->method('submit')->willReturn(
            FulfillmentResult::success(
                externalOrderId: 'SUP-ORDER-12345',
                cost: 15.00,
                shippingFee: 5.00,
                trackingNumber: 'TRK-IDEM-001',
                carrier: 'USPS'
            )
        );

        $service = new FulfillmentService($mockProvider);
        $fulfillment = $service->fulfillOrder($order);

        $this->assertNotNull($fulfillment);
        $this->assertEquals('SUBMITTED', $fulfillment->fulfillment_status);

        // Verify attempt ledger entry
        $attempts = FulfillmentAttempt::where('fulfillment_id', $fulfillment->id)->get();
        $this->assertCount(1, $attempts);
        $this->assertEquals(1, $attempts->first()->attempt_number);
        $this->assertEquals('SUCCESS', $attempts->first()->status);
        $this->assertStringContainsString('ORD-IDEM-', $attempts->first()->idempotency_key);
    }
}
