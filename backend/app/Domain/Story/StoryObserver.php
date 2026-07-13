<?php

namespace App\Domain\Story;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Story;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StoryObserver
{
    public function creating(Story $story): void
    {
        if (empty($story->slug)) {
            $story->slug = Str::slug($story->title);
        }
    }

    public function created(Story $story): void
    {
        Cache::forget('stories:list');
        DashboardCache::clearAll();
    }

    public function updating(Story $story): void
    {
        if ($story->isDirty('title') && ! $story->isDirty('slug')) {
            $story->slug = Str::slug($story->title);
        }
    }

    public function updated(Story $story): void
    {
        Cache::forget("stories:{$story->slug}");
        Cache::forget('stories:list');
        DashboardCache::clearAll();
    }

    public function deleted(Story $story): void
    {
        Cache::forget("stories:{$story->slug}");
        Cache::forget('stories:list');
        DashboardCache::clearAll();
    }
}
