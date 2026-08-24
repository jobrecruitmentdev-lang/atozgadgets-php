<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\OutboxEvent;
use App\Models\Order;
use App\Services\Order\FulfillmentService;
use Illuminate\Support\Facades\Log;

class FulfillOrderOutbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public OutboxEvent $outboxEvent;

    public function __construct(OutboxEvent $outboxEvent)
    {
        $this->outboxEvent = $outboxEvent;
    }

    public function handle(): void
    {
        if ($this->outboxEvent->status === 'PROCESSED') {
            return;
        }

        $orderId = $this->outboxEvent->payload['order_id'] ?? $this->outboxEvent->aggregate_id;
        $order = Order::find($orderId);

        if (!$order || $order->payment_status !== 'paid') {
            $this->outboxEvent->update(['status' => 'FAILED']);
            return;
        }

        try {
            $supplierOrder = FulfillmentService::fulfill($order);
            $this->outboxEvent->update([
                'status' => 'PROCESSED',
            ]);
        } catch (\Throwable $e) {
            $this->outboxEvent->update([
                'attempts' => $this->outboxEvent->attempts + 1,
                'status' => 'FAILED',
            ]);
            Log::error('Outbox CJ Fulfillment failed:', ['order_id' => $orderId, 'error' => $e->getMessage()]);
        }
    }
}
