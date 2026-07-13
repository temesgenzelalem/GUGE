<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Contracts\AuditRepositoryInterface;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditRepository implements AuditRepositoryInterface
{
    public function query(array $filters = []): Builder
    {
        $query = AuditLog::query();

        if (! empty($filters['actor_id'])) {
            $query->where('actor_id', $filters['actor_id']);
        }

        if (! empty($filters['action'])) {
            $query->where('action', 'ILIKE', "%{$filters['action']}%");
        }

        if (! empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        return $query;
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->query($filters)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?AuditLog
    {
        return AuditLog::find($id);
    }

    public function create(array $data): AuditLog
    {
        return AuditLog::create($data);
    }
}
