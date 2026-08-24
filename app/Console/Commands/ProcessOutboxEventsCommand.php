<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutboxEvent;
use App\Models\Order;
use App\Services\Fulfillment\FulfillmentService;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Log;

class ProcessOutboxEventsCommand extends Command
{
    protected $signature = 'outbox:process {--limit=20 : Number of pending outbox events to process} {--budget=45 : Maximum execution seconds}';
    protected $description = 'Process pending outbox events with atomic claiming, stale recovery, and shared-hosting runtime budget';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $budget = (int) $this->option('budget');
        $deadline = microtime(true) + $budget;
        $workerId = gethostname() . ':' . getmypid();

        // 1. Recover stale claims (> 5 minutes stuck in CLAIMED)
        $staleCount = OutboxEvent::where('status', 'CLAIMED')
            ->where('claimed_at', '<', now()->subMinutes(5))
            ->update([
                'status' => 'PENDING',
                'claimed_by' => null,
            ]);

        if ($staleCount > 0) {
            $this->warn("Recovered {$staleCount} stale outbox claims back to PENDING.");
        }

        // 2. Fetch pending events
        $events = OutboxEvent::where('status', 'PENDING')
            ->where('attempts', '<', 5)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($events->isEmpty()) {
            $this->info('No pending outbox events to process.');
            return 0;
        }

        $this->info("Processing {$events->count()} outbox events with {$budget}s runtime budget...");

        foreach ($events as $event) {
            // Check deadline before every item
            if (microtime(true) >= $deadline) {
                $this->warn("Runtime budget exhausted ({$budget}s). Yielding cleanly to next cron run.");
                break;
            }

            // Atomic claim
            $claimed = OutboxEvent::where('id', $event->id)
                ->where('status', 'PENDING')
                ->update([
                    'status' => 'CLAIMED',
                    'claimed_at' => now(),
                    'claimed_by' => $workerId,
                ]);

            if (!$claimed) {
                continue; // Claimed by a concurrent process
            }

            try {
                $orderId = $event->payload['order_id'] ?? $event->aggregate_id;
                $order = Order::find($orderId);

                // Invariant 1: Authoritative Payment Ledger Check
                $ledger = PaymentService::getLedgerSummary($order);

                if ($order && $ledger->is_fully_paid) {
                    $fulfillment = $order->fulfillments()->whereIn('fulfillment_status', ['PENDING', 'EXCEPTION', 'RECONCILIATION_REQUIRED'])->first();
                    if (!$fulfillment) {
                        $fulfillment = FulfillmentService::createFulfillmentsForOrder($order);
                    }

                    $result = FulfillmentService::executeFulfillment($fulfillment);

                    if ($result->success) {
                        $event->update([
                            'status' => 'PROCESSED',
                            'processed_at' => now(),
                            'claimed_by' => null,
                        ]);
                        $this->info("Order #{$orderId} fulfilled successfully via provider ({$result->externalOrderId}).");
                    } else {
                        $attempts = $event->attempts + 1;
                        $event->update([
                            'attempts' => $attempts,
                            'status' => $attempts >= 5 ? 'DEAD_LETTER' : 'FAILED',
                            'error_message' => $result->errorMessage,
                            'last_error_code' => 'FULFILLMENT_FAILED',
                            'last_attempt_at' => now(),
                            'claimed_by' => null,
                        ]);
                        $this->warn("Order #{$orderId} fulfillment exception: {$result->errorMessage}");
                    }
                } else {
                    $attempts = $event->attempts + 1;
                    $event->update([
                        'attempts' => $attempts,
                        'status' => 'FAILED',
                        'error_message' => 'Payment not fully captured in authoritative ledger (Net Paid: $' . $ledger->net_paid . ')',
                        'last_error_code' => 'LEDGER_UNPAID',
                        'last_attempt_at' => now(),
                        'claimed_by' => null,
                    ]);
                }
            } catch (\Throwable $e) {
                $attempts = $event->attempts + 1;
                $event->update([
                    'attempts' => $attempts,
                    'status' => $attempts >= 5 ? 'DEAD_LETTER' : 'FAILED',
                    'error_message' => $e->getMessage(),
                    'last_error_code' => 'UNHANDLED_EXCEPTION',
                    'last_attempt_at' => now(),
                    'claimed_by' => null,
                ]);

                $this->error("Error processing event {$event->id}: " . $e->getMessage());
                Log::error('Outbox process error:', ['event_id' => $event->id, 'error' => $e->getMessage()]);
            }
        }

        return 0;
    }
}
