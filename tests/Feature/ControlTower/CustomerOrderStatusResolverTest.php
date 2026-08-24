<?php

namespace Tests\Feature\ControlTower;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Fulfillment;
use App\Models\Shipment;
use App\Services\Order\CustomerOrderStatusResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerOrderStatusResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_payment_pending()
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-PEND-' . uniqid(),
            'total_amount' => 50.00,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $resolved = CustomerOrderStatusResolver::resolve($order);
        $this->assertEquals('Payment Pending', $resolved['status']);
    }

    public function test_resolves_order_confirmed_when_paid()
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-PAID-' . uniqid(),
            'total_amount' => 50.00,
            'payment_status' => 'paid',
            'status' => 'pending',
        ]);

        $resolved = CustomerOrderStatusResolver::resolve($order);
        $this->assertEquals('Order Confirmed', $resolved['status']);
    }

    public function test_resolves_preparing_order_when_fulfillment_submitted()
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-SUBM-' . uniqid(),
            'total_amount' => 50.00,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        Fulfillment::create([
            'order_id' => $order->id,
            'fulfillment_status' => 'SUBMITTED',
        ]);

        $resolved = CustomerOrderStatusResolver::resolve($order->fresh());
        $this->assertEquals('Preparing Order', $resolved['status']);
    }

    public function test_resolves_in_transit_when_shipment_in_transit()
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-TRANS-' . uniqid(),
            'total_amount' => 50.00,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $fulfillment = Fulfillment::create([
            'order_id' => $order->id,
            'fulfillment_status' => 'PROCESSING',
        ]);

        Shipment::create([
            'order_id' => $order->id,
            'fulfillment_id' => $fulfillment->id,
            'tracking_number' => 'TRK123456',
            'status' => 'IN_TRANSIT',
        ]);

        $resolved = CustomerOrderStatusResolver::resolve($order->fresh());
        $this->assertEquals('In Transit', $resolved['status']);
    }

    public function test_resolves_delivered_when_all_shipments_delivered()
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-DELV-' . uniqid(),
            'total_amount' => 50.00,
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);

        $fulfillment = Fulfillment::create([
            'order_id' => $order->id,
            'fulfillment_status' => 'PROCESSING',
        ]);

        Shipment::create([
            'order_id' => $order->id,
            'fulfillment_id' => $fulfillment->id,
            'tracking_number' => 'TRK123456',
            'status' => 'DELIVERED',
        ]);

        $resolved = CustomerOrderStatusResolver::resolve($order->fresh());
        $this->assertEquals('Delivered', $resolved['status']);
    }
}
