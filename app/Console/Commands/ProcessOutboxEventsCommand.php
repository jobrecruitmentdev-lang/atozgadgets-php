<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutboxEvent;
use App\Models\Order;
use App\Services\Cj\CjSupplierAdapter;
use Illuminate\Support\Facades\Log;

class ProcessOutboxEventsCommand extends Command
{
    protected $signature = 'outbox:process {--limit=20 : Number of pending outbox events to process}';
    protected $description = 'Process pending outbox events for Hostinger Shared Cron execution';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $events = OutboxEvent::where('status', 'PENDING')
            ->where('attempts', '<', 5)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($events->isEmpty()) {
            $this->info('No pending outbox events to process.');
            return 0;
        }

        $this->info("Processing {$events->count()} outbox events...");

        foreach ($events as $event) {
            try {
                $orderId = $event->payload['order_id'] ?? $event->aggregate_id;
                $order = Order::find($orderId);

                if ($order && in_array(strtolower($order->payment_status ?? ''), ['paid', 'completed', 'success'])) {
                    // Execute fulfillment via CjSupplierAdapter
                    $supplierOrder = CjSupplierAdapter::fulfillOrder($order);
                    
                    $event->status = 'PROCESSED';
                    $event->processed_at = now();
                    $event->save();

                    $this->info("Order #{$orderId} fulfilled successfully to supplier ({$supplierOrder->external_order_id}).");
                } else {
                    $event->attempts = $event->attempts + 1;
                    $event->status = 'FAILED';
                    $event->error_message = 'Order not found or payment not paid';
                    $event->save();
                }
            } catch (\Throwable $e) {
                $attempts = $event->attempts + 1;
                $event->attempts = $attempts;
                $event->status = $attempts >= 5 ? 'DEAD_LETTER' : 'FAILED';
                $event->error_message = $e->getMessage();
                $event->save();

                $this->error("Error processing event {$event->id}: " . $e->getMessage());
                Log::error('Outbox process error:', ['event_id' => $event->id, 'error' => $e->getMessage()]);
            }
        }

        return 0;
    }
}
