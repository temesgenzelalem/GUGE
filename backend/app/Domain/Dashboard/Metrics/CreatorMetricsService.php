<?php

namespace App\Domain\Dashboard\Metrics;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Creator;

class CreatorMetricsService
{
    public function getMetrics(): array
    {
        return DashboardCache::remember(self::cacheKey(), function () {
            $counts = Creator::query()
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw("SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS verified")
                ->selectRaw("SUM(CASE WHEN status IN ('pending', 'pending_review') THEN 1 ELSE 0 END) AS pending")
                ->selectRaw("SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) AS suspended")
                ->first();

            return array_map(fn ($v) => (int) ($v ?? 0), $counts->toArray());
        });
    }

    public static function cacheKey(): string
    {
        return DashboardCache::CREATORS;
    }
}
