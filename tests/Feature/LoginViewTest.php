<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginViewTest extends TestCase
{
    /**
     * A basic test to verify the login view loads successfully with premium aesthetics.
     *
     * @return void
     */
    public function test_login_view_loads_successfully()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
        $response->assertSee('Enter your credentials to access your account');
        $response->assertSee('auth-container');
        $response->assertSee('btn-primary');
    }
}
