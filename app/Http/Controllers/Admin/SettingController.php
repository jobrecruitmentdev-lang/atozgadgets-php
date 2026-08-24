<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'AtoZGadgets'),
            'support_email' => Setting::get('support_email', 'support@atozgadgetz.com'),
            'currency' => Setting::get('currency', 'USD'),
            'currency_symbol' => Setting::get('currency_symbol', '$'),
            'free_shipping_threshold' => Setting::get('free_shipping_threshold', '50.00'),
            'default_markup' => Setting::get('default_markup', '2.5'),
            'cj_api_email' => Setting::get('cj_api_email', env('CJ_API_EMAIL', config('services.cj.email', ''))),
            'cj_api_key' => Setting::get('cj_api_key', env('CJ_API_KEY', config('services.cj.key', ''))),
            'cj_sandbox_mode' => Setting::get('cj_sandbox_mode', '0'),
            'cj_auto_fulfill' => Setting::get('cj_auto_fulfill', '1'),
            'paypal_mode' => Setting::get('paypal_mode', 'sandbox'),
            'paypal_sandbox_client_id' => Setting::get('paypal_sandbox_client_id', Setting::get('paypal_client_id', env('PAYPAL_SANDBOX_CLIENT_ID', ''))),
            'paypal_sandbox_client_secret' => Setting::get('paypal_sandbox_client_secret', Setting::get('paypal_client_secret', env('PAYPAL_SANDBOX_CLIENT_SECRET', ''))),
            'paypal_live_client_id' => Setting::get('paypal_live_client_id', env('PAYPAL_LIVE_CLIENT_ID', '')),
            'paypal_live_client_secret' => Setting::get('paypal_live_client_secret', env('PAYPAL_LIVE_CLIENT_SECRET', '')),
            'paypal_client_id' => Setting::get('paypal_client_id', ''),
            'payoneer_account' => Setting::get('payoneer_account', ''),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $allowedKeys = [
            'store_name', 'support_email', 'currency', 'currency_symbol',
            'free_shipping_threshold', 'default_markup', 'cj_api_email',
            'cj_api_key', 'cj_sandbox_mode', 'cj_auto_fulfill',
            'paypal_mode', 'paypal_sandbox_client_id', 'paypal_sandbox_client_secret',
            'paypal_live_client_id', 'paypal_live_client_secret',
            'paypal_client_id', 'paypal_client_secret', 'payoneer_account'
        ];

        $protectedKeys = [
            'cj_api_email', 'cj_api_key',
            'paypal_sandbox_client_id', 'paypal_sandbox_client_secret',
            'paypal_live_client_id', 'paypal_live_client_secret',
            'paypal_client_id', 'paypal_client_secret'
        ];

        $data = $request->only($allowedKeys);

        foreach ($data as $key => $value) {
            // Secret & Credential Protection: Do not wipe existing stored credentials if blank in form
            if (in_array($key, $protectedKeys)) {
                if (empty($value) && Setting::where('key', $key)->whereNotNull('value')->where('value', '!=', '')->exists()) {
                    continue;
                }
            }

            $group = 'general';
            $isSecret = false;

            if (str_starts_with($key, 'cj_')) {
                $group = 'cj';
                if ($key === 'cj_api_key') $isSecret = true;
            } elseif (str_starts_with($key, 'paypal_') || str_starts_with($key, 'payoneer_')) {
                $group = 'payments';
                if (str_contains($key, 'secret') || str_contains($key, 'key')) $isSecret = true;
            }

            Setting::set($key, is_null($value) ? '' : (string)$value, $group, $isSecret);
        }

        // Targeted cache clearing
        Cache::forget('admin_dashboard_metrics');
        Cache::forget('store_settings');
        Cache::forget('cj_access_token');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Store configuration updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Store configuration updated successfully!');
    }

    public function toggleCjSandbox(Request $request)
    {
        $current = Setting::get('cj_sandbox_mode', '0');
        $new = $current === '1' ? '0' : '1';
        Setting::set('cj_sandbox_mode', $new, 'cj');
        Cache::forget('cj_access_token');
        
        return response()->json([
            'success' => true,
            'sandbox_mode' => $new === '1',
            'message' => $new === '1' ? 'CJ Sandbox Mode is now ACTIVE (Demo Data).' : 'CJ Live API Mode is now ACTIVE (Production).'
        ]);
    }

    public function testCjConnection(Request $request)
    {
        $email = $request->has('cj_api_email') ? $request->input('cj_api_email') : Setting::get('cj_api_email');
        $apiKey = $request->has('cj_api_key') ? $request->input('cj_api_key') : Setting::get('cj_api_key');

        $result = \App\Services\Cj\CjAuthService::testConnection($email, $apiKey);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}