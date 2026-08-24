<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsPersistenceGuardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'email' => 'admin_guard_' . uniqid() . '@test.com',
            'first_name' => 'Admin',
            'last_name' => 'Guard',
            'password' => bcrypt('password'),
            'mobile' => '99' . rand(10000000, 99999999),
            'role_id' => 1,
            'is_active' => true,
        ]);
    }

    public function test_saving_general_settings_does_not_wipe_cj_or_paypal_credentials()
    {
        // 1. Initial save of CJ and PayPal credentials
        $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'cj_api_email' => 'admin@atozgadgets.com',
            'cj_api_key' => 'CJ_LIVE_SECRET_KEY_12345',
            'paypal_sandbox_client_id' => 'PAYPAL_SANDBOX_CLIENT_XYZ',
            'paypal_sandbox_client_secret' => 'PAYPAL_SANDBOX_SECRET_XYZ',
        ]);

        $this->assertEquals('admin@atozgadgets.com', Setting::get('cj_api_email'));
        $this->assertEquals('CJ_LIVE_SECRET_KEY_12345', Setting::get('cj_api_key'));
        $this->assertEquals('PAYPAL_SANDBOX_CLIENT_XYZ', Setting::get('paypal_sandbox_client_id'));

        // 2. Now simulate submitting the General Settings tab with empty CJ and PayPal inputs
        $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name' => 'AtoZGadgets Updated Name',
            'support_email' => 'support@atozgadgets.com',
            'currency' => 'USD',
            'cj_api_email' => '', // submitted blank from general tab
            'cj_api_key' => '',   // submitted blank from general tab
            'paypal_sandbox_client_id' => '', // submitted blank
            'paypal_sandbox_client_secret' => '', // submitted blank
        ]);

        // 3. Assert credentials remain 100% intact and preserved!
        $this->assertEquals('AtoZGadgets Updated Name', Setting::get('store_name'));
        $this->assertEquals('admin@atozgadgets.com', Setting::get('cj_api_email'));
        $this->assertEquals('CJ_LIVE_SECRET_KEY_12345', Setting::get('cj_api_key'));
        $this->assertEquals('PAYPAL_SANDBOX_CLIENT_XYZ', Setting::get('paypal_sandbox_client_id'));
        $this->assertEquals('PAYPAL_SANDBOX_SECRET_XYZ', Setting::get('paypal_sandbox_client_secret'));
    }
}
