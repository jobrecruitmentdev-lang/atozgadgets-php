<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLifecycleAndValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_with_blank_mobile_persists_strict_null_in_database()
    {
        $payload = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe.null@example.com',
            'mobile' => '',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $user = User::where('email', 'john.doe.null@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->mobile, 'User mobile MUST be strict NULL in database when blank, not empty string.');
    }

    public function test_registration_with_valid_mobile_persists_formatted_number()
    {
        $payload = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'mobile' => '+12025550199',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect('/login');

        $user = User::where('email', 'jane.smith@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('+12025550199', $user->mobile);
    }

    public function test_registration_rejects_invalid_email()
    {
        $payload = [
            'first_name' => 'Invalid',
            'last_name' => 'Email',
            'email' => 'not-an-email',
            'mobile' => null,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->post('/register', $payload);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', ['first_name' => 'Invalid']);
    }

    public function test_registration_rejects_duplicate_email()
    {
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'mobile' => null,
            'password' => Hash::make('password123'),
            'role_id' => 3,
            'is_active' => 1,
        ]);

        $payload = [
            'first_name' => 'Duplicate',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'mobile' => '+12025550188',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->post('/register', $payload);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_rejects_duplicate_mobile_number()
    {
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'Mobile',
            'email' => 'existing.phone@example.com',
            'mobile' => '+12025559999',
            'password' => Hash::make('password123'),
            'role_id' => 3,
            'is_active' => 1,
        ]);

        $payload = [
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'new.user.phone@example.com',
            'mobile' => '+12025559999',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->post('/register', $payload);

        $response->assertSessionHasErrors(['mobile']);
    }

    public function test_registration_rejects_weak_password_under_8_characters()
    {
        $payload = [
            'first_name' => 'Weak',
            'last_name' => 'Pass',
            'email' => 'weak.pass@example.com',
            'mobile' => null,
            'password' => '12345',
            'password_confirmation' => '12345',
        ];

        $response = $this->post('/register', $payload);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_registration_rejects_password_confirmation_mismatch()
    {
        $payload = [
            'first_name' => 'Mismatch',
            'last_name' => 'Pass',
            'email' => 'mismatch.pass@example.com',
            'mobile' => null,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'DifferentPass123!',
        ];

        $response = $this->post('/register', $payload);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_full_login_logout_login_lifecycle()
    {
        $user = User::create([
            'first_name' => 'Lifecycle',
            'last_name' => 'Tester',
            'email' => 'lifecycle@example.com',
            'mobile' => null,
            'password' => Hash::make('SuperSecret123!'),
            'role_id' => 3,
            'is_active' => 1,
        ]);

        // 1. Initial Login
        $loginRes = $this->post('/login', [
            'email' => 'lifecycle@example.com',
            'password' => 'SuperSecret123!',
        ]);
        $loginRes->assertRedirect(route('account.dashboard'));
        $this->assertAuthenticatedAs($user);

        // 2. Logout
        $logoutRes = $this->post('/logout');
        $logoutRes->assertRedirect(route('login'));
        $this->assertGuest();

        // 3. Login Again
        $reLoginRes = $this->post('/login', [
            'email' => 'lifecycle@example.com',
            'password' => 'SuperSecret123!',
        ]);
        $reLoginRes->assertRedirect(route('account.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_auth_views_render_eye_toggle_buttons()
    {
        $loginView = $this->get('/login');
        $loginView->assertStatus(200);
        $loginView->assertSee('password-toggle-btn');
        $loginView->assertSee('togglePasswordVisibility(this)');

        $registerView = $this->get('/register');
        $registerView->assertStatus(200);
        $registerView->assertSee('password-toggle-btn');
        $registerView->assertSee('togglePasswordVisibility(this)');
    }
}
