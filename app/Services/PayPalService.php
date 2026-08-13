<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PayPalService
{
    private static function getBaseUrl()
    {
        return config('paypal.mode') === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
    }

    private static function getAccessToken()
    {
        return Cache::remember('paypal_access_token', 3200, function () {
            $response = Http::withBasicAuth(config('paypal.client_id'), config('paypal.client_secret'))
                ->timeout(10)
                ->asForm()
                ->post(self::getBaseUrl() . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->failed()) {
                throw new \Exception('Failed to obtain PayPal Access Token');
            }

            return $response->json('access_token');
        });
    }

    public static function createOrder($amount)
    {
        $response = Http::withToken(self::getAccessToken())
            ->timeout(15)
            ->post(self::getBaseUrl() . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($amount, 2, '.', '')
                        ]
                    ]
                ]
            ]);

        return $response->json();
    }

    public static function captureOrder($paypalOrderId)
    {
        $response = Http::withToken(self::getAccessToken())
            ->timeout(15)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post(self::getBaseUrl() . "/v2/checkout/orders/{$paypalOrderId}/capture");

        return $response->json();
    }
}
