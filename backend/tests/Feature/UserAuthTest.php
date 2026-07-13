<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)
                ->where('message', 'Registration successful')
                ->has('data.user.id')
                ->has('data.token')
                ->etc()
            );

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)
                ->where('message', 'Login successful')
                ->has('data.token')
                ->etc()
            );
    }

    public function test_user_can_request_password_reset(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Password reset link sent successfully.']);
    }

    public function test_user_can_logout_and_revoke_current_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Logged out successfully']);
    }

    public function test_authenticated_user_can_update_profile_and_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile', [
            'name' => 'Jane Updated',
        ]);

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)
                ->where('message', 'Profile updated successfully')
                ->where('data.name', 'Jane Updated')
                ->etc()
            );

        $response = $this->patchJson('/api/password', [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Password updated successfully.']);
    }
}
