<?php

namespace App\Domain\Audit\Contracts;

use App\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuditRepositoryInterface
{
    public function query(array $filters = []);

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?AuditLog;

    public function create(array $data): AuditLog;
}
