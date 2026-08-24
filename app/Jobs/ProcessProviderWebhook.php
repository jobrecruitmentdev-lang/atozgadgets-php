<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ProviderEvent;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Log;

class ProcessProviderWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ProviderEvent $event;

    public function __construct(ProviderEvent $event)
    {
        $this->event = $event;
    }

    public function handle(): void
    {
        if ($this->event->processing_status === 'PROCESSED') {
            return;
        }

        $payload = $this->event->payload;
        $eventType = $this->event->event_type;

        try {
            if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
                $resource = $payload['resource'] ?? [];
                $customId = $resource['custom_id'] ?? null;
                $captureId = $resource['id'] ?? null;
                $amount = (float)($resource['amount']['value'] ?? 0.0);
                $currency = $resource['amount']['currency_code'] ?? 'USD';

                $order = null;
                if ($customId) {
                    $order = Order::where('order_number', $customId)->orWhere('id', $customId)->first();
                }

                if ($order && !in_array(strtolower($order->payment_status ?? ''), ['paid', 'completed', 'success'])) {
                    $preCapturedData = [
                        'id' => $captureId,
                        'status' => 'COMPLETED',
                        'amount' => [
                            'value' => (string)$amount,
                            'currency_code' => $currency,
                        ],
                        'purchase_units' => [[
                            'payments' => [
                                'captures' => [[
                                    'id' => $captureId,
                                    'status' => 'COMPLETED',
                                    'amount' => ['value' => (string)$amount, 'currency_code' => $currency],
                                ]]
                            ]
                        ]]
                    ];
                    PaymentService::captureAndConfirm($order, $captureId ?? 'WH-' . $this->event->event_id, 'paypal', $preCapturedData);
                }
            }

            $this->event->update([
                'processing_status' => 'PROCESSED',
                'processed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $attempts = $this->event->attempts + 1;
            $status = $attempts >= 3 ? 'DEAD_LETTER' : 'FAILED';

            $this->event->update([
                'attempts' => $attempts,
                'processing_status' => $status,
            ]);

            Log::error('Webhook processing failed:', ['event_id' => $this->event->event_id, 'error' => $e->getMessage()]);
        }
    }
}
