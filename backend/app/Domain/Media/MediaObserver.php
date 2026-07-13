<?php

namespace App\Domain\Media;

use App\Domain\Dashboard\DashboardCache;
use App\Domain\Media\Events\MediaCreated;
use App\Domain\Media\Events\MediaDeleted;
use App\Models\Media;
use Illuminate\Support\Facades\Cache;

class MediaObserver
{
    public function created(Media $media): void
    {
        Cache::forget('media:list');
        DashboardCache::clearAll();
        event(new MediaCreated($media));
    }

    public function updated(Media $media): void
    {
        Cache::forget('media:list');
        Cache::forget("media:{$media->uuid}");
    }

    public function deleted(Media $media): void
    {
        Cache::forget('media:list');
        Cache::forget("media:{$media->uuid}");
        DashboardCache::clearAll();
        event(new MediaDeleted($media));
    }
}
