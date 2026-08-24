<?php

namespace Tests\Feature\Resilience;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\OutboxEvent;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\FulfillmentProvider;
use Illuminate\Support\Facades\Artisan;

class OutboxAtomicClaimTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        FulfillmentProvider::firstOrCreate(
            ['code' => 'cj'],
            ['name' => 'CJ Dropshipping', 'is_active' => true]
        );
    }

    public function test_two_workers_do_not_double_claim_same_event()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-001',
            'subtotal' => 50.00,
            'total_amount' => 50.00,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'transaction_id' => 'CAP-12345',
            'amount' => 50.00,
            'payment_method' => 'paypal',
            'status' => 'success',
        ]);

        PaymentTransaction::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'type' => 'CAPTURE',
            'amount' => 50.00,
            'currency' => 'USD',
            'provider' => 'paypal',
            'provider_transaction_id' => 'CAP-12345',
            'status' => 'completed',
        ]);

        $event = OutboxEvent::create([
            'event_name' => 'ORDER_PAID',
            'aggregate_type' => 'Order',
            'aggregate_id' => $order->id,
            'payload' => ['order_id' => $order->id],
            'status' => 'PENDING',
            'attempts' => 0,
        ]);

        // Simulated Worker 1 acquires atomic lock
        $worker1Claimed = OutboxEvent::where('id', $event->id)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'CLAIMED',
                'claimed_at' => now(),
                'claimed_by' => 'worker-1',
            ]);

        // Simulated Worker 2 attempts to claim the same event
        $worker2Claimed = OutboxEvent::where('id', $event->id)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'CLAIMED',
                'claimed_at' => now(),
                'claimed_by' => 'worker-2',
            ]);

        $this->assertEquals(1, $worker1Claimed);
        $this->assertEquals(0, $worker2Claimed);
    }

    public function test_stale_claims_are_recovered_after_5_minutes()
    {
        $event = OutboxEvent::create([
            'event_name' => 'ORDER_PAID',
            'aggregate_type' => 'Order',
            'aggregate_id' => 999,
            'payload' => ['order_id' => 999],
            'status' => 'CLAIMED',
            'claimed_at' => now()->subMinutes(6),
            'claimed_by' => 'dead-worker-pid-99',
            'attempts' => 0,
        ]);

        // Run outbox command
        Artisan::call('outbox:process', ['--limit' => 10, '--budget' => 5]);

        $event->refresh();
        // Stale claim should have been reset from CLAIMED to PENDING and processed/failed due to missing order
        $this->assertNotEquals('dead-worker-pid-99', $event->claimed_by);
    }
}
