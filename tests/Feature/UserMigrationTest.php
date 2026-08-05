<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserMigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can be created with the new schema fields.
     *
     * @return void
     */
    public function test_user_can_be_created_with_new_schema()
    {
        $user = User::updateOrCreate(
            ['email' => 'john.doe@example.com'],
            [
                'role_id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'mobile' => '+1234567890',
                'password' => bcrypt('password123'),
                'profile_image' => 'profile.jpg',
                'is_active' => true,
            ]
        );

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'mobile' => '+1234567890',
            'first_name' => 'John',
            'is_active' => 1
        ]);

        $this->assertEquals('John', $user->first_name);
        $this->assertTrue($user->is_active);
    }
}
