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
            'cj_api_email' => Setting::get('cj_api_email', config('services.cj.email', '')),
            'cj_api_key' => Setting::get('cj_api_key', config('services.cj.key', '')),
            'cj_auto_fulfill' => Setting::get('cj_auto_fulfill', '1'),
            'cj_wallet_alert' => Setting::get('cj_wallet_alert', '50.00'),
            'paypal_client_id' => Setting::get('paypal_client_id', ''),
            'paypal_mode' => Setting::get('paypal_mode', 'sandbox'),
            'payoneer_account' => Setting::get('payoneer_account', ''),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);

        foreach ($data as $key => $value) {
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

        Cache::flush();

        return redirect()->back()->with('success', 'Store configuration updated successfully!');
    }
}