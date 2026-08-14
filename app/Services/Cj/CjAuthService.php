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

    public static function getAccessToken()
    {
        // Use Laravel Cache to store token
        $token = Cache::get('cj_access_token');
        if ($token) {
            return $token;
        }

        $email = config('services.cj.email');
        $apiKey = config('services.cj.key');

        if (!$email || !$apiKey) {
            Log::warning('⚠️ CJ API Credentials missing in .env. Using CJ Sandbox Mode.');
            return 'SANDBOX_DEMO_TOKEN';
        }

        try {
            usleep(1100000); // 1.1s throttle for rate limit

            $response = Http::post(self::getApiBaseUrl() . '/authentication/getAccessToken', [
                'email' => $email,
                'password' => $apiKey,
            ]);

            $data = $response->json();

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

    public static function getAuthHeaders()
    {
        $token = self::getAccessToken();
        return [
            'Content-Type' => 'application/json',
            'CJ-Access-Token' => $token,
        ];
    }
}
