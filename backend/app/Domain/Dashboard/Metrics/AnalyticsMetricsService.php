<?php

namespace App\Domain\Dashboard\Metrics;

use App\Domain\Dashboard\DashboardCache;
use App\Models\AuditLog;

class AnalyticsMetricsService
{
    public function getMetrics(): array
    {
        return DashboardCache::remember(self::cacheKey(), function () {
            return [
                'latest_audit_events' => AuditLog::query()
                    ->select('id', 'actor_id', 'action', 'auditable_type', 'auditable_id', 'created_at')
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get()
                    ->toArray(),
            ];
        });
    }

    public static function cacheKey(): string
    {
        return DashboardCache::ANALYTICS;
    }
}
