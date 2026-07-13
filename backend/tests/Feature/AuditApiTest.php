<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuditApiTest extends TestCase
{
    private User $admin;

    private AuditLog $auditLog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->auditLog = AuditLog::factory()->forActor($this->admin)->create();
    }

    public function test_admin_can_list_audit_logs(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/admin/audit-logs');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success', 'message', 'data', 'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    public function test_admin_can_view_single_audit_log(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/audit-logs/{$this->auditLog->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $this->auditLog->id)
            ->assertJsonPath('data.action', $this->auditLog->action);
    }

    public function test_audit_log_not_found_returns_404(): void
    {
        Sanctum::actingAs($this->admin);

        $this->getJson('/api/admin/audit-logs/99999')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_admin_can_filter_audit_logs_by_action(): void
    {
        Sanctum::actingAs($this->admin);

        AuditLog::factory()->forActor($this->admin)->create(['action' => 'login']);

        $response = $this->getJson('/api/admin/audit-logs?action=login');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_filter_audit_logs_by_actor(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/audit-logs?actor_id={$this->admin->id}");

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_guest_cannot_access_audit_logs(): void
    {
        $this->getJson('/api/admin/audit-logs')->assertUnauthorized();
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/audit-logs')->assertForbidden();
    }

    public function test_audit_logs_are_paginated(): void
    {
        Sanctum::actingAs($this->admin);

        AuditLog::factory()->forActor($this->admin)->count(5)->create();

        $response = $this->getJson('/api/admin/audit-logs?per_page=3');

        $response->assertOk()
            ->assertJsonPath('meta.per_page', 3)
            ->assertJsonPath('meta.total', 6);
    }
}
