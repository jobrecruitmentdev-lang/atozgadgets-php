<?php

namespace App\Services\Cj;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CjAuthService
{
    private static function getApiBaseUrl()
    {
        return env('CJ_API_BASE_URL', 'https://developers.cjdropshipping.com/api2.0/v1');
    }

    public static function isSandboxMode(): bool
    {
        return \App\Models\Setting::get('cj_sandbox_mode', '0') === '1';
    }

    public static function getAccessToken()
    {
        if (self::isSandboxMode()) {
            return 'SANDBOX_DEMO_TOKEN';
        }

        // Use Laravel Cache to store token
        $token = Cache::get('cj_access_token');
        if ($token) {
            return $token;
        }

        if (app()->environment('testing')) {
            return 'MOCK_TEST_TOKEN_12345';
        }

        $email = \App\Models\Setting::get('cj_api_email') ?: config('services.cj.email');
        $apiKey = \App\Models\Setting::get('cj_api_key') ?: config('services.cj.key');

        if (!$email || !$apiKey) {
            Log::warning('⚠️ CJ API Credentials missing in Database and .env. Using CJ Sandbox Mode.');
            return 'SANDBOX_DEMO_TOKEN';
        }

        try {
            if (!app()->environment('testing')) {
                usleep(1100000); // 1.1s throttle for rate limit
            }

            // 1. Try standard CJ API 2.0 apiKey mode first
            $response = Http::timeout(12)->post(self::getApiBaseUrl() . '/authentication/getAccessToken', [
                'apiKey' => $apiKey,
            ]);

            $data = $response->json();

            // 2. If not successful and email provided, fallback to email+password
            if ((!$response->successful() || !isset($data['code']) || $data['code'] !== 200) && !empty($email)) {
                $response = Http::timeout(12)->post(self::getApiBaseUrl() . '/authentication/getAccessToken', [
                    'email' => $email,
                    'password' => $apiKey,
                ]);
                $data = $response->json();
            }

            if ($response->successful() && isset($data['code']) && $data['code'] === 200 && isset($data['data'])) {
                $token = $data['data']['accessToken'];
                $expiryDate = $data['data']['accessTokenExpiryDate'] ?? ($data['data']['tokenExpiryDate'] ?? null);
                $expiryMs = $expiryDate ? strtotime($expiryDate) * 1000 : 0;
                if (!$expiryMs) {
                    $expiryMs = (time() + 86400) * 1000;
                }
                
                $ttlSeconds = floor(($expiryMs - (time() * 1000) - 300000) / 1000);
                if ($ttlSeconds > 0) {
                    Cache::put('cj_access_token', $token, $ttlSeconds);
                }
                
                return $token;
            } else {
                Log::warning('⚠️ CJ Auth Response Code != 200. Falling back to sandbox mode:', ['message' => $data['message'] ?? '']);
                return 'SANDBOX_DEMO_TOKEN';
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ CJ Auth Request Error:', ['error' => $e->getMessage()]);
            return 'SANDBOX_DEMO_TOKEN';
        }
    }

    public static function testConnection($email, $apiKey)
    {
        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'Please provide a valid CJ API Key.'
            ];
        }

        try {
            $startTime = microtime(true);

            // 1. Test apiKey mode first
            $response = Http::timeout(10)->post(self::getApiBaseUrl() . '/authentication/getAccessToken', [
                'apiKey' => $apiKey,
            ]);
            $latency = round((microtime(true) - $startTime) * 1000);
            $data = $response->json();

            // 2. Fallback to email+password mode if needed
            if ((!$response->successful() || !isset($data['code']) || $data['code'] !== 200) && !empty($email)) {
                $response = Http::timeout(10)->post(self::getApiBaseUrl() . '/authentication/getAccessToken', [
                    'email' => $email,
                    'password' => $apiKey,
                ]);
                $data = $response->json();
            }

            if ($response->successful() && isset($data['code']) && $data['code'] === 200 && isset($data['data'])) {
                return [
                    'success' => true,
                    'latency_ms' => $latency,
                    'message' => "Connection successful! CJ Token verified in {$latency}ms.",
                    'data' => [
                        'token_expiry' => $data['data']['accessTokenExpiryDate'] ?? '180 Days',
                    ]
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Authentication failed: Invalid CJ API credentials or API key inactive.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Network/Connection Error: ' . $e->getMessage()
            ];
        }
    }

    public static function getAuthHeaders()
    {
        $token = self::getAccessToken();
        return [
            'Content-Type' => 'application/json',
            'CJ-Access-Token' => $token,
        ];
    }
}
