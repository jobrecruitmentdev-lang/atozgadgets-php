<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Services\Cj\CjAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CjSandboxToggleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_sandbox_' . uniqid() . '@atozgadgets.com',
            'role_id' => 1,
            'is_active' => true,
        ]);

        $this->customer = User::factory()->create([
            'email' => 'cust_sandbox_' . uniqid() . '@example.com',
            'role_id' => 3,
            'is_active' => true,
        ]);
    }

    public function test_ajax_toggle_sandbox_endpoint_switches_modes()
    {
        // 1. Initially default to 0
        Setting::set('cj_sandbox_mode', '0', 'cj');
        $this->assertFalse(CjAuthService::isSandboxMode());

        // 2. Toggle to ON (1) via AJAX
        $response = $this->actingAs($this->admin)
                         ->postJson('/admin/settings/toggle-cj-sandbox');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'sandbox_mode' => true,
        ]);

        $this->assertTrue(CjAuthService::isSandboxMode());
        $this->assertEquals('SANDBOX_DEMO_TOKEN', CjAuthService::getAccessToken());

        // 3. Toggle to OFF (0) via AJAX
        $response2 = $this->actingAs($this->admin)
                          ->postJson('/admin/settings/toggle-cj-sandbox');

        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'sandbox_mode' => false,
        ]);

        $this->assertFalse(CjAuthService::isSandboxMode());
    }

    public function test_unauthenticated_or_customer_cannot_toggle_sandbox()
    {
        $guestResponse = $this->postJson('/admin/settings/toggle-cj-sandbox');
        $guestResponse->assertStatus(401);

        $customerResponse = $this->actingAs($this->customer)
                                 ->postJson('/admin/settings/toggle-cj-sandbox');
        $customerResponse->assertStatus(403);
    }

    public function test_ajax_settings_update_saves_to_database_and_returns_json()
    {
        $response = $this->actingAs($this->admin)
                         ->postJson('/admin/settings', [
                             'store_name' => 'AtoZGadgets Pro Edition',
                             'cj_api_email' => 'custom_cj@atoz.com',
                             'cj_api_key' => 'custom_key_12345',
                             'cj_auto_fulfill' => '1',
                         ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertEquals('AtoZGadgets Pro Edition', Setting::get('store_name'));
        $this->assertEquals('custom_cj@atoz.com', Setting::get('cj_api_email'));
        $this->assertEquals('custom_key_12345', Setting::get('cj_api_key'));
    }

    public function test_test_cj_connection_endpoint_validates_inputs()
    {
        $response = $this->actingAs($this->admin)
                         ->postJson('/admin/settings/test-cj-connection', [
                             'cj_api_email' => '',
                             'cj_api_key' => '',
                         ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }
}
