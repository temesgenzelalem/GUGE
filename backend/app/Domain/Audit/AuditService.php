<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Contracts\AuditRepositoryInterface;
use App\Domain\Audit\Contracts\AuditServiceInterface;
use App\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class AuditService implements AuditServiceInterface
{
    public function __construct(protected AuditRepositoryInterface $repository) {}

    public function listAuditLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = sprintf('audit:list:%s:%d', md5(json_encode($filters)), $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(5), fn () => $this->repository->paginate($filters, $perPage)
        );
    }

    public function getAuditLog(int $id): ?AuditLog
    {
        return $this->repository->findById($id);
    }

    public function recordAudit(array $data): AuditLog
    {
        Cache::forget('audit:list');

        return $this->repository->create($data);
    }
}
