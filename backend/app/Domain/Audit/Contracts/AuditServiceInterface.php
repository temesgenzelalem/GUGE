<?php

namespace App\Domain\Audit\Contracts;

use App\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuditServiceInterface
{
    public function listAuditLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getAuditLog(int $id): ?AuditLog;

    public function recordAudit(array $data): AuditLog;
}
