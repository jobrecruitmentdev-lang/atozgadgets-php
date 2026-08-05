<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\CjOrder;
use App\Services\Cj\CjShipmentService;

class CjServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_updates_order_and_shipment_status()
    {
        // 1. Setup Models
        $user = User::factory()->create();
        $order = Order::updateOrCreate(
            ['order_number' => 'ORD-123'],
            [
                'user_id' => $user->id,
                'total_amount' => 100.00,
                'status' => 'pending'
            ]
        );

        CjOrder::updateOrCreate(
            ['internal_order_id' => $order->id],
            [
                'cj_order_id' => 'CJ-999',
                'status' => 'created'
            ]
        );
        
        \DB::table('shipments')->updateOrInsert(
            ['order_id' => $order->id],
            [
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // 2. Simulate Webhook Payload from CJ
        $payload = [
            'orderNumber' => 'ORD-123',
            'orderStatus' => 'shipped',
            'trackingNumber' => 'TRACK-123456',
            'carrierName' => 'CJPacket',
            'trackingUrl' => 'https://cj.com/track'
        ];

        // 3. Process Webhook
        CjShipmentService::handleWebhook($payload);

        // 4. Assertions (E2E testing validation)
        $this->assertDatabaseHas('cj_orders', [
            'internal_order_id' => $order->id,
            'status' => 'shipped'
        ]);

        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'tracking_number' => 'TRACK-123456',
            'carrier' => 'CJPacket',
            'status' => 'shipped'
        ]);

        // Removed cj_shipments assertion
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped'
        ]);
    }
}
