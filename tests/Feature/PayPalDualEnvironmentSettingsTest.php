<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Services\Payment\PayPalGateway;

class PayPalDualEnvironmentSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\PaymentProviderAccount::query()->delete();
        \Illuminate\Support\Facades\Cache::flush();

        $this->admin = User::create([
            'email' => 'admin_paypal_' . uniqid() . '@test.com',
            'first_name' => 'Admin',
            'last_name' => 'PayPal',
            'password' => bcrypt('password'),
            'mobile' => '99' . rand(10000000, 99999999),
            'role_id' => 1,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_save_both_sandbox_and_live_credentials()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'paypal_mode' => 'sandbox',
                'paypal_sandbox_client_id' => 'SANDBOX_CLIENT_ID_XYZ',
                'paypal_sandbox_client_secret' => 'SANDBOX_SECRET_123',
                'paypal_live_client_id' => 'LIVE_CLIENT_ID_ABC',
                'paypal_live_client_secret' => 'LIVE_SECRET_789',
            ]);

        $response->assertRedirect();

        $this->assertEquals('sandbox', Setting::get('paypal_mode'));
        $this->assertEquals('SANDBOX_CLIENT_ID_XYZ', Setting::get('paypal_sandbox_client_id'));
        $this->assertEquals('SANDBOX_SECRET_123', Setting::get('paypal_sandbox_client_secret'));
        $this->assertEquals('LIVE_CLIENT_ID_ABC', Setting::get('paypal_live_client_id'));
        $this->assertEquals('LIVE_SECRET_789', Setting::get('paypal_live_client_secret'));
    }

    public function test_gateway_resolves_correct_credentials_based_on_active_mode()
    {
        Setting::set('paypal_sandbox_client_id', 'SB_CLIENT_999');
        Setting::set('paypal_sandbox_client_secret', 'SB_SECRET_999');
        Setting::set('paypal_live_client_id', 'LIVE_CLIENT_888');
        Setting::set('paypal_live_client_secret', 'LIVE_SECRET_888');

        $gateway = new PayPalGateway();
        $reflector = new \ReflectionClass($gateway);
        $method = $reflector->getMethod('getCredentials');
        $method->setAccessible(true);

        // 1. Sandbox Mode
        Setting::set('paypal_mode', 'sandbox');
        $creds = $method->invoke($gateway);
        $this->assertEquals('SB_CLIENT_999', $creds['client_id']);
        $this->assertEquals('SB_SECRET_999', $creds['client_secret']);

        // 2. Live Mode
        Setting::set('paypal_mode', 'live');
        $creds = $method->invoke($gateway);
        $this->assertEquals('LIVE_CLIENT_888', $creds['client_id']);
        $this->assertEquals('LIVE_SECRET_888', $creds['client_secret']);
    }

    public function test_storefront_checkout_injects_matching_active_client_id()
    {
        Setting::set('paypal_sandbox_client_id', 'SB_INJECT_TEST_CLIENT');
        Setting::set('paypal_live_client_id', 'LIVE_INJECT_TEST_CLIENT');

        $cart = [1 => ['name' => 'Test Gadget', 'price' => 29.99, 'quantity' => 1]];

        // Sandbox view
        Setting::set('paypal_mode', 'sandbox');
        $response = $this->withSession(['cart' => $cart])->get(route('store.checkout'));
        $response->assertStatus(200);
        $response->assertSee('client-id=SB_INJECT_TEST_CLIENT', false);

        // Live view
        Setting::set('paypal_mode', 'live');
        $response = $this->withSession(['cart' => $cart])->get(route('store.checkout'));
        $response->assertStatus(200);
        $response->assertSee('client-id=LIVE_INJECT_TEST_CLIENT', false);
    }
}
