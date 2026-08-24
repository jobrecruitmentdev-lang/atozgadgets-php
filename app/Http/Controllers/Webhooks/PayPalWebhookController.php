<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProviderEvent;
use App\Jobs\ProcessProviderWebhook;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $eventId = $payload['id'] ?? ('evt_' . md5($request->getContent()));
        $eventType = $payload['event_type'] ?? 'UNKNOWN';

        // 1. Idempotent Ingestion into provider_events
        $event = ProviderEvent::firstOrCreate(
            ['event_id' => $eventId],
            [
                'provider' => 'paypal',
                'event_type' => $eventType,
                'payload' => $payload,
                'signature_verified' => true,
                'processing_status' => 'RECEIVED',
                'attempts' => 0,
            ]
        );

        // 2. Dispatch Async Processing Job (or process synchronously if queue is sync)
        try {
            ProcessProviderWebhook::dispatch($event);
        } catch (\Throwable $e) {
            Log::error('Webhook dispatch error:', ['error' => $e->getMessage()]);
        }

        // Return instant 200 OK to PayPal
        return response()->json(['status' => 'success', 'event_id' => $eventId], 200);
    }
}
