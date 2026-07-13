<?php

namespace App\Domain\Dashboard\Metrics;

use App\Domain\Dashboard\DashboardCache;
use App\Models\User;

class UserMetricsService
{
    public function getMetrics(): array
    {
        return DashboardCache::remember(self::cacheKey(), function () {
            $today = now()->startOfDay();
            $weekStart = now()->startOfWeek();

            $counts = User::query()
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active")
                ->selectRaw("SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) AS suspended")
                ->selectRaw('SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) AS verified')
                ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS new_today', [$today])
                ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS new_this_week', [$weekStart])
                ->first();

            return array_map(
                fn ($v) => (int) ($v ?? 0),
                $counts->toArray()
            );
        });
    }

    public static function cacheKey(): string
    {
        return DashboardCache::USERS;
    }
}
