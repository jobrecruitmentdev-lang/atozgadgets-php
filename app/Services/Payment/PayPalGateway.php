<?php

namespace App\Services\Payment;

use App\Models\Setting;
use App\Models\PaymentProviderAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PayPalGateway implements PaymentGatewayInterface
{
    private function getEnvironment(): string
    {
        return Setting::get('paypal_mode', 'sandbox');
    }

    private function getBaseUrl(): string
    {
        return $this->getEnvironment() === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function getCredentials(): array
    {
        $env = $this->getEnvironment();
        $account = PaymentProviderAccount::where('provider', 'paypal')
            ->where('environment', $env)
            ->where('is_active', true)
            ->first();

        if ($account) {
            return [
                'client_id' => $account->client_id,
                'client_secret' => $account->client_secret,
            ];
        }

        if ($env === 'live') {
            $clientId = Setting::get('paypal_live_client_id') ?: Setting::get('paypal_client_id', config('paypal.client_id'));
            $clientSecret = Setting::get('paypal_live_client_secret') ?: Setting::get('paypal_client_secret', config('paypal.client_secret'));
        } else {
            $clientId = Setting::get('paypal_sandbox_client_id') ?: Setting::get('paypal_client_id', config('paypal.client_id'));
            $clientSecret = Setting::get('paypal_sandbox_client_secret') ?: Setting::get('paypal_client_secret', config('paypal.client_secret'));
        }

        return [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ];
    }

    public function getAccessToken(): string
    {
        $creds = $this->getCredentials();
        $cacheKey = 'paypal_oauth_' . md5($creds['client_id'] . $this->getEnvironment());

        return Cache::remember($cacheKey, 3200, function () use ($creds) {
            $response = Http::withBasicAuth($creds['client_id'], $creds['client_secret'])
                ->timeout(10)
                ->asForm()
                ->post($this->getBaseUrl() . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->failed()) {
                $err = $response->json();
                $desc = $err['error_description'] ?? $response->body();
                if ($creds['client_id'] === $creds['client_secret']) {
                    throw new \Exception("PayPal Configuration Error: Your PayPal Client ID was entered into both Client ID and Secret fields. Please paste your actual PayPal Client Secret (from developer.paypal.com) in Admin Settings > Payment Gateways.");
                }
                throw new \Exception("PayPal Authentication Failed: {$desc}. Please check your PayPal Client ID & Secret in Admin Settings > Payment Gateways.");
            }

            return $response->json('access_token');
        });
    }

    public function createOrder(float $amount, string $orderReference, string $currency = 'USD'): array
    {
        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'default',
                    'custom_id' => $orderReference,
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ],
            ],
            'application_context' => [
                'brand_name' => Setting::get('store_name', 'AtoZGadgets'),
                'landing_page' => 'NO_PREFERENCE',
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING',
            ]
        ];

        $response = Http::withToken($this->getAccessToken())
            ->timeout(15)
            ->withHeaders([
                'PayPal-Request-Id' => 'req_' . Str::uuid(),
            ])
            ->post($this->getBaseUrl() . '/v2/checkout/orders', $payload);

        if ($response->failed()) {
            throw new \Exception('PayPal Order Creation Failed: ' . $response->body());
        }

        return $response->json();
    }

    public function captureOrder(string $providerOrderId): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->timeout(15)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'PayPal-Request-Id' => 'cap_' . md5($providerOrderId),
            ])
            ->post($this->getBaseUrl() . "/v2/checkout/orders/{$providerOrderId}/capture");

        return $response->json();
    }

    public function verifyWebhookSignature(array $headers, string $rawBody): bool
    {
        $webhookId = Setting::get('paypal_webhook_id', config('paypal.webhook_id', env('PAYPAL_WEBHOOK_ID', '')));
        
        // In local/testing mock environments without a live webhook ID
        if (app()->environment('testing') && empty($webhookId)) {
            return !empty($headers['PAYPAL-TRANSMISSION-SIG'] ?? $headers['paypal-transmission-sig'] ?? null);
        }

        if (empty($webhookId)) {
            \Illuminate\Support\Facades\Log::error('PayPal Webhook Verification Failed: PAYPAL_WEBHOOK_ID is not configured in settings or environment.');
            return false;
        }

        // Normalize header keys (handle case insensitivity)
        $normalizedHeaders = [];
        foreach ($headers as $key => $value) {
            $normalizedHeaders[strtoupper($key)] = is_array($value) ? ($value[0] ?? '') : $value;
        }

        $authAlgo = $normalizedHeaders['PAYPAL-AUTH-ALGO'] ?? null;
        $certUrl = $normalizedHeaders['PAYPAL-CERT-URL'] ?? null;
        $transmissionId = $normalizedHeaders['PAYPAL-TRANSMISSION-ID'] ?? null;
        $transmissionSig = $normalizedHeaders['PAYPAL-TRANSMISSION-SIG'] ?? null;
        $transmissionTime = $normalizedHeaders['PAYPAL-TRANSMISSION-TIME'] ?? null;

        if (!$authAlgo || !$certUrl || !$transmissionId || !$transmissionSig || !$transmissionTime) {
            \Illuminate\Support\Facades\Log::warning('PayPal Webhook Verification Failed: Missing required cryptographic transmission headers.', [
                'has_auth_algo' => !empty($authAlgo),
                'has_cert_url' => !empty($certUrl),
                'has_transmission_id' => !empty($transmissionId),
                'has_transmission_sig' => !empty($transmissionSig),
                'has_transmission_time' => !empty($transmissionTime),
            ]);
            return false;
        }

        // Cert URL safety check (must be a genuine PayPal domain)
        if (!str_starts_with($certUrl, 'https://api.paypal.com/') && 
            !str_starts_with($certUrl, 'https://api-m.paypal.com/') && 
            !str_starts_with($certUrl, 'https://api.sandbox.paypal.com/') && 
            !str_starts_with($certUrl, 'https://api-m.sandbox.paypal.com/')) {
            \Illuminate\Support\Facades\Log::error('PayPal Webhook Verification Failed: Untrusted cert_url domain: ' . $certUrl);
            return false;
        }

        $decodedBody = json_decode($rawBody);
        if (!$decodedBody) {
            \Illuminate\Support\Facades\Log::error('PayPal Webhook Verification Failed: Invalid JSON body.');
            return false;
        }

        $verificationPayload = [
            'auth_algo' => $authAlgo,
            'cert_url' => $certUrl,
            'transmission_id' => $transmissionId,
            'transmission_sig' => $transmissionSig,
            'transmission_time' => $transmissionTime,
            'webhook_id' => $webhookId,
            'webhook_event' => $decodedBody,
        ];

        try {
            $response = Http::withToken($this->getAccessToken())
                ->timeout(10)
                ->post($this->getBaseUrl() . '/v1/notifications/verify-webhook-signature', $verificationPayload);

            if ($response->successful()) {
                $status = $response->json('verification_status');
                return strtoupper($status) === 'SUCCESS';
            }

            \Illuminate\Support\Facades\Log::error('PayPal Webhook Signature API call failed: ' . $response->body());
            return false;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PayPal Webhook Signature Verification Exception: ' . $e->getMessage());
            return false;
        }
    }
}
