<?php

namespace App\Domain\Dashboard\Metrics;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Region;
use App\Models\Story;

class TravelMetricsService
{
    public function getMetrics(): array
    {
        return DashboardCache::remember(self::cacheKey(), function () {
            $topRegions = Region::query()
                ->select('id', 'name', 'slug')
                ->orderByDesc('created_at')
                ->limit(3)
                ->get();

            $topStories = Story::query()
                ->select('id', 'title', 'slug', 'view_count')
                ->orderByDesc('view_count')
                ->limit(3)
                ->get();

            return [
                'top_regions' => $topRegions->map(fn ($region) => [
                    'id' => $region->id,
                    'name' => $region->name,
                    'slug' => $region->slug,
                    'view_count' => 0,
                ])->toArray(),
                'most_viewed_destinations' => $topRegions->map(fn ($region) => [
                    'id' => $region->id,
                    'name' => $region->name,
                    'slug' => $region->slug,
                    'view_count' => 0,
                ])->toArray(),
                'most_viewed_stories' => $topStories->map(fn ($story) => [
                    'id' => $story->id,
                    'title' => $story->title,
                    'slug' => $story->slug,
                    'view_count' => $story->view_count,
                ])->toArray(),
            ];
        });
    }

    public static function cacheKey(): string
    {
        return DashboardCache::TRAVEL;
    }
}
