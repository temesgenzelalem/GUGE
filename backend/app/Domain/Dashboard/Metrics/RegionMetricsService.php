<?php

namespace App\Domain\Dashboard\Metrics;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Region;

class RegionMetricsService
{
    public function getMetrics(): array
    {
        return DashboardCache::remember(self::cacheKey(), function () {
            $counts = Region::query()
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw("SUM(CASE WHEN status = 'featured' THEN 1 ELSE 0 END) AS featured")
                ->selectRaw("SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS published")
                ->selectRaw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS draft")
                ->first();

            return array_map(
                fn ($v) => (int) ($v ?? 0),
                $counts->toArray()
            );
        });
    }

    public static function cacheKey(): string
    {
        return DashboardCache::REGIONS;
    }
}
