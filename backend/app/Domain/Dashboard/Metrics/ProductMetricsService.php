<?php

namespace App\Domain\Dashboard\Metrics;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Product;

class ProductMetricsService
{
    public function getMetrics(): array
    {
        return DashboardCache::remember(self::cacheKey(), function () {
            $counts = Product::query()
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw("SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS published")
                ->selectRaw("SUM(CASE WHEN status IN ('pending', 'pending_review') THEN 1 ELSE 0 END) AS pending")
                ->selectRaw('SUM(CASE WHEN hidden = true THEN 1 ELSE 0 END) AS hidden')
                ->selectRaw('SUM(CASE WHEN featured = true THEN 1 ELSE 0 END) AS featured')
                ->first();

            return array_map(
                fn ($v) => (int) ($v ?? 0),
                $counts->toArray()
            );
        });
    }

    public static function cacheKey(): string
    {
        return DashboardCache::PRODUCTS;
    }
}
