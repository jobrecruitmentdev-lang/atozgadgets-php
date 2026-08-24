<?php

namespace Tests\Feature\Resilience;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Fulfillment;
use App\Models\FulfillmentProvider;
use App\Models\ShipmentCarrier;
use App\Services\Fulfillment\FulfillmentService;
use App\Services\Fulfillment\FulfillmentProviderInterface;
use App\Services\Fulfillment\FulfillmentResult;
use App\Services\Fulfillment\ExternalOrderLookupResult;
use App\Services\Fulfillment\CancellationResult;
use App\Services\Fulfillment\TrackingResult;
use App\Services\Fulfillment\InventoryResult;
use App\Models\SupplierProduct;

class MockReconcilingProvider implements FulfillmentProviderInterface
{
    public int $submitCallCount = 0;
    public int $lookupCallCount = 0;
    public bool $simulateTimeoutOnSubmit = false;
    public bool $orderFoundOnLookup = false;

    public function submit(Fulfillment $fulfillment): FulfillmentResult
    {
        $this->submitCallCount++;
        if ($this->simulateTimeoutOnSubmit) {
            throw new \Exception('cURL error 28: Operation timed out after 8001 milliseconds');
        }
        return FulfillmentResult::success(
            externalOrderId: 'CJ-EXT-9988',
            cost: 15.00,
            shippingFee: 4.50,
            trackingNumber: 'CJTRK12345678',
            carrier: 'CJPacket Fast'
        );
    }

    public function findExistingOrder(Fulfillment $fulfillment): ExternalOrderLookupResult
    {
        $this->lookupCallCount++;
        if ($this->orderFoundOnLookup) {
            return ExternalOrderLookupResult::found(
                externalOrderId: 'CJ-EXT-9988',
                status: 'SUBMITTED',
                trackingNumber: 'CJTRK12345678',
                carrierName: 'CJPacket Fast',
                cost: 15.00,
                shippingFee: 4.50
            );
        }
        return ExternalOrderLookupResult::notFound();
    }

    public function cancel(Fulfillment $fulfillment): CancellationResult { return CancellationResult::success(); }
    public function getTracking(Fulfillment $fulfillment): TrackingResult { return TrackingResult::success('IN_TRANSIT', 'CJTRK12345678'); }
    public function getInventory(SupplierProduct $product): InventoryResult { return InventoryResult::success(10, 'available'); }
}

class SupplierReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        FulfillmentProvider::firstOrCreate(
            ['code' => 'cj'],
            ['name' => 'CJ Dropshipping', 'is_active' => true]
        );
        ShipmentCarrier::firstOrCreate(
            ['internal_code' => 'standard'],
            ['customer_name' => 'Standard Delivery', 'is_active' => true]
        );
    }

    public function test_timeout_transitions_to_reconciliation_required_and_adopts_order_without_duplicate_submit()
    {
        $user = User::factory()->create();
        $cat = \App\Models\Category::create(['name' => 'Smart Watches', 'slug' => 'smart-watches', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Smart Watch Pro',
            'slug' => 'smart-watch-pro',
            'sku' => 'AZG-SW-001',
            'price' => 79.99,
            'stock_quantity' => 10,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-RECON-001',
            'subtotal' => 79.99,
            'total_amount' => 79.99,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'transaction_id' => 'CAP-PAY-77',
            'amount' => 79.99,
            'payment_method' => 'paypal',
            'status' => 'success',
        ]);

        PaymentTransaction::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'type' => 'CAPTURE',
            'amount' => 79.99,
            'currency' => 'USD',
            'provider' => 'paypal',
            'provider_transaction_id' => 'CAP-PAY-77',
            'status' => 'completed',
        ]);

        $fulfillment = FulfillmentService::createFulfillmentsForOrder($order);

        // Attempt 1: Simulate Supplier Timeout
        $mockProvider = new MockReconcilingProvider();
        $mockProvider->simulateTimeoutOnSubmit = true;

        FulfillmentService::executeWithAdapter($fulfillment, $mockProvider);

        $fulfillment->refresh();
        $this->assertEquals('RECONCILIATION_REQUIRED', $fulfillment->fulfillment_status);
        $this->assertEquals(1, $mockProvider->submitCallCount);

        // Attempt 2: Order exists on CJ (upstream was actually created before timeout dropped)
        $mockProvider->simulateTimeoutOnSubmit = false;
        $mockProvider->orderFoundOnLookup = true;

        $result = FulfillmentService::executeWithAdapter($fulfillment, $mockProvider);

        $fulfillment->refresh();
        $this->assertTrue($result->success);
        $this->assertEquals('SUBMITTED', $fulfillment->fulfillment_status);
        $this->assertEquals(1, $mockProvider->lookupCallCount);
        // CRITICAL INVARIANT PROOF: Total submit() POST calls must remain exactly 1!
        $this->assertEquals(1, $mockProvider->submitCallCount);
        $this->assertDatabaseHas('supplier_orders', [
            'order_id' => $order->id,
            'external_order_id' => 'CJ-EXT-9988',
        ]);
    }
}
