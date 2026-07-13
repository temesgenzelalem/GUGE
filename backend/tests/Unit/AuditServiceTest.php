<?php

namespace Tests\Unit;

use App\Domain\Audit\AuditService;
use App\Domain\Audit\Contracts\AuditRepositoryInterface;
use App\Models\AuditLog;
use App\Models\User;
use Tests\TestCase;

class AuditServiceTest extends TestCase
{
    private AuditService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuditService($this->app->make(AuditRepositoryInterface::class));
    }

    public function test_record_audit_persists_to_database(): void
    {
        $user = User::factory()->create();

        $log = $this->service->recordAudit([
            'actor_id' => $user->id,
            'action' => 'created',
            'auditable_type' => 'App\\Models\\Region',
            'auditable_id' => 1,
            'metadata' => ['note' => 'test'],
        ]);

        $this->assertInstanceOf(AuditLog::class, $log);
        $this->assertDatabaseHas('audit_logs', ['actor_id' => $user->id, 'action' => 'created']);
    }

    public function test_get_audit_log_returns_correct_record(): void
    {
        $user = User::factory()->create();
        $log = AuditLog::factory()->forActor($user)->create();

        $found = $this->service->getAuditLog($log->id);

        $this->assertNotNull($found);
        $this->assertEquals($log->id, $found->id);
    }

    public function test_get_audit_log_returns_null_for_missing(): void
    {
        $found = $this->service->getAuditLog(999999);

        $this->assertNull($found);
    }

    public function test_list_audit_logs_returns_paginator(): void
    {
        $user = User::factory()->create();
        AuditLog::factory()->forActor($user)->count(5)->create();

        $result = $this->service->listAuditLogs([], 20);

        $this->assertEquals(5, $result->total());
    }

    public function test_list_audit_logs_filters_by_action(): void
    {
        $user = User::factory()->create();
        AuditLog::factory()->forActor($user)->create(['action' => 'login']);
        AuditLog::factory()->forActor($user)->create(['action' => 'created']);

        $result = $this->service->listAuditLogs(['action' => 'login'], 20);

        $this->assertEquals(1, $result->total());
    }

    public function test_list_audit_logs_filters_by_actor(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        AuditLog::factory()->forActor($user1)->count(2)->create();
        AuditLog::factory()->forActor($user2)->count(3)->create();

        $result = $this->service->listAuditLogs(['actor_id' => $user1->id], 20);

        $this->assertEquals(2, $result->total());
    }
}
