<?php

namespace App\Domain\Region;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Region;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RegionObserver
{
    public function creating(Region $region): void
    {
        if (empty($region->slug)) {
            $region->slug = Str::slug($region->name);
        }
    }

    public function created(Region $region): void
    {
        Cache::forget('regions:list');
        DashboardCache::clearAll();
    }

    public function updating(Region $region): void
    {
        if ($region->isDirty('name') && ! $region->isDirty('slug')) {
            $region->slug = Str::slug($region->name);
        }
    }

    public function updated(Region $region): void
    {
        Cache::forget("regions:{$region->slug}");
        Cache::forget('regions:list');
        DashboardCache::clearAll();
    }

    public function deleted(Region $region): void
    {
        Cache::forget("regions:{$region->slug}");
        Cache::forget('regions:list');
        DashboardCache::clearAll();
    }
}
