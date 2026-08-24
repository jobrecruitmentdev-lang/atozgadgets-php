<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutOtpBypassAndNavbarDedupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::firstOrCreate(['slug' => 'tech-gadgets'], ['name' => 'Tech Gadgets', 'status' => 'active']);
        Category::firstOrCreate(['slug' => 'electronics'], ['name' => 'Electronics', 'status' => 'active']);
        Category::firstOrCreate(['slug' => 'audio-sound'], ['name' => 'Audio & Sound', 'status' => 'active']);
    }

    public function test_checkout_send_otp_generates_and_returns_dev_otp_in_testing_env()
    {
        $payload = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe.testing@example.com',
            'phone' => '1234567890',
            'address1' => '123 Main St',
            'city' => 'New York',
            'postal_code' => '10001',
            'country' => 'United States'
        ];

        $response = $this->postJson(route('store.checkout.send-otp'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertNotEmpty($response->json('dev_otp'));
        $this->assertEquals(6, strlen($response->json('dev_otp')));
    }

    public function test_checkout_verify_otp_accepts_master_testing_code_123456()
    {
        // Set up session shipping
        session(['checkout_shipping' => ['email' => 'john@test.com', 'phone' => '1234567890']]);

        $response = $this->postJson(route('store.checkout.verify-otp'), [
            'otp' => '123456'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertTrue(session('checkout_otp_verified'));
    }

    public function test_checkout_verify_otp_accepts_session_generated_otp()
    {
        $otp = '884921';
        session([
            'checkout_otp' => $otp,
            'checkout_otp_expires_at' => time() + 600,
        ]);

        $response = $this->postJson(route('store.checkout.verify-otp'), [
            'otp' => $otp
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertTrue(session('checkout_otp_verified'));
    }

    public function test_navbar_categories_are_deduplicated_and_clean()
    {
        // Create duplicate category name with different slug and redundant alias
        Category::firstOrCreate(['slug' => 'tech-gadgets-dup'], ['name' => 'Tech Gadgets', 'status' => 'active']);
        Category::firstOrCreate(['slug' => 'tech-alias'], ['name' => 'Tech', 'status' => 'active']);

        $response = $this->get(route('store.home'));
        $response->assertStatus(200);

        // Verify categories view data has no duplicates
        $globalCategories = $response->viewData('globalCategories');
        $this->assertNotNull($globalCategories);

        $names = $globalCategories->pluck('name')->toArray();
        $this->assertEquals(count($names), count(array_unique($names)));
    }
}
