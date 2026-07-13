<?php

namespace App\Domain\Audit;

use App\Domain\Dashboard\DashboardCache;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;

class AuditObserver
{
    public function created(AuditLog $auditLog): void
    {
        Cache::forget('audit:list');
        DashboardCache::clearAll();
    }
}
