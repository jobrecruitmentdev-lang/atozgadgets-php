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
        $rawContent = $request->getContent();
        $payload = json_decode($rawContent, true) ?: $request->all();
        $headers = $request->headers->all();

        $gateway = new \App\Services\Payment\PayPalGateway();
        $isValidSignature = $gateway->verifyWebhookSignature($headers, $rawContent);

        if (!$isValidSignature) {
            Log::warning('PayPal Webhook Rejected: Invalid or forged cryptographic signature.', [
                'ip' => $request->ip(),
                'headers' => array_keys($headers),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature verification'], 401);
        }

        $eventId = $payload['id'] ?? ('evt_' . md5($rawContent));
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

        // 2. Dispatch Async Processing Job
        try {
            ProcessProviderWebhook::dispatch($event);
        } catch (\Throwable $e) {
            Log::error('Webhook dispatch error:', ['error' => $e->getMessage()]);
        }

        // Return instant 200 OK to PayPal so it does not retry
        return response()->json(['status' => 'success', 'event_id' => $eventId], 200);
    }
}
