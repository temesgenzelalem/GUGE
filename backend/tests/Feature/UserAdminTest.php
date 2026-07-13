<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    private User $admin;

    private User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->targetUser = User::factory()->create();
    }

    public function test_admin_can_list_users(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/admin/users');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success', 'message', 'data',
                'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    public function test_admin_can_view_single_user(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/users/{$this->targetUser->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $this->targetUser->id)
            ->assertJsonPath('data.email', $this->targetUser->email);
    }

    public function test_admin_can_create_user(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/admin/users', [
            'name' => 'New Admin User',
            'email' => 'newadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'moderator',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Admin User')
            ->assertJsonPath('data.role', 'moderator');

        $this->assertDatabaseHas('users', ['email' => 'newadmin@example.com']);
    }

    public function test_admin_can_update_user(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/admin/users/{$this->targetUser->id}", [
            'name' => 'Updated Name',
            'email' => $this->targetUser->email,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name');
    }

    public function test_admin_can_delete_other_user(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/users/{$this->targetUser->id}");

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', ['id' => $this->targetUser->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        Sanctum::actingAs($this->admin);

        $this->deleteJson("/api/admin/users/{$this->admin->id}")
            ->assertForbidden();
    }

    public function test_user_list_can_be_searched(): void
    {
        Sanctum::actingAs($this->admin);

        User::factory()->create(['name' => 'Abebe Bekele']);

        $response = $this->getJson('/api/admin/users?search=Abebe');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_user_list_can_be_filtered_by_role(): void
    {
        Sanctum::actingAs($this->admin);

        User::factory()->create(['role' => 'moderator']);

        $response = $this->getJson('/api/admin/users?role=moderator');

        $response->assertOk();
        $users = $response->json('data.data');
        foreach ($users as $user) {
            $this->assertEquals('moderator', $user['role']);
        }
    }

    public function test_user_resource_includes_status(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/users/{$this->targetUser->id}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['status']]);
    }

    public function test_guest_cannot_list_users(): void
    {
        $this->getJson('/api/admin/users')->assertUnauthorized();
    }

    public function test_non_admin_cannot_list_users(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/users')->assertForbidden();
    }

    public function test_user_creation_requires_unique_email(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/admin/users', [
            'name' => 'Duplicate',
            'email' => $this->targetUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['email']]);
    }
}
