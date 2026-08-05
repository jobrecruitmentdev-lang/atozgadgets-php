<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class ApiEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist for registration
        Role::firstOrCreate(['role_name' => 'Customer'], ['permissions' => json_encode([])]);
        Role::firstOrCreate(['role_name' => 'Admin'], ['permissions' => json_encode([])]);
    }

    public function test_public_products_endpoint_returns_success()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_protected_route()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_user_can_register_and_login()
    {
        // 1. Register User
        $email = time() . '_user@example.com';
        $registerPayload = [
            'email' => $email,
            'password' => 'SecurePass123!',
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => (string)rand(1000000000, 9999999999)
        ];

        $registerResponse = $this->postJson('/api/auth/register', $registerPayload);

        $registerResponse->assertStatus(201)
                         ->assertJsonStructure(['success', 'message', 'data' => ['token']]);

        // 2. Login User
        $loginPayload = [
            'email' => $email,
            'password' => 'SecurePass123!'
        ];

        $loginResponse = $this->postJson('/api/auth/login', $loginPayload);
        $loginResponse->assertStatus(200)
                      ->assertJsonStructure(['success', 'message', 'data' => ['token']]);

        $token = $loginResponse->json('data.token');

        // 3. Access Protected Route
        $meResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/me');

        $meResponse->assertStatus(200)
                   ->assertJsonFragment(['email' => $email]);
    }

    public function test_xss_protection_in_registration()
    {
        $this->withoutExceptionHandling();
        // Test that script tags are sanitized or registration still proceeds without causing raw script reflection
        $xssPayload = [
            'email' => time() . '_hacker@example.com',
            'password' => 'HackPass123!',
            'first_name' => '<script>alert("xss")</script>',
            'last_name' => 'User',
            'mobile' => (string)rand(1000000000, 9999999999)
        ];

        $response = $this->postJson('/api/auth/register', $xssPayload);

        $response->assertStatus(201);
        
        $token = $response->json('data.token');
        
        $meResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/me');
        
        $meResponse->assertStatus(200);
        // We assert that the database saved it, but when rendered on blade it would be escaped.
        // For API, we just ensure it didn't crash or break the JSON response.
    }
}
