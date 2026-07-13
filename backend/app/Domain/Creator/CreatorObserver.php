<?php

namespace App\Domain\Creator;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Creator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CreatorObserver
{
    public function creating(Creator $creator): void
    {
        if (empty($creator->full_name) && ! empty($creator->name)) {
            $creator->full_name = $creator->name;
        }

        if (empty($creator->name) && ! empty($creator->full_name)) {
            $creator->name = $creator->full_name;
        }

        if (empty($creator->slug)) {
            $creator->slug = Str::slug($creator->full_name ?? $creator->name);
        }
    }

    public function created(Creator $creator): void
    {
        Cache::forget('creators:list');
        DashboardCache::clearAll();
    }

    public function updating(Creator $creator): void
    {
        if ($creator->isDirty('full_name') && ! $creator->isDirty('slug')) {
            $creator->slug = Str::slug($creator->full_name);
        }

        if ($creator->isDirty('full_name') && empty($creator->name)) {
            $creator->name = $creator->full_name;
        }
    }

    public function updated(Creator $creator): void
    {
        Cache::forget("creators:{$creator->slug}");
        Cache::forget('creators:list');
        DashboardCache::clearAll();
    }

    public function deleted(Creator $creator): void
    {
        Cache::forget("creators:{$creator->slug}");
        Cache::forget('creators:list');
        DashboardCache::clearAll();
    }
}
