<?php

namespace App\Domain\Tag;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TagObserver
{
    public function creating(Tag $tag): void
    {
        if (empty($tag->slug) && ! empty($tag->name)) {
            $tag->slug = Str::slug($tag->name);
        }

        if (! empty($tag->name)) {
            $tag->name = ucfirst(trim($tag->name));
        }
    }

    public function created(Tag $tag): void
    {
        Cache::forget('tags:all');
        Cache::forget('tags:list');
    }

    public function updating(Tag $tag): void
    {
        if ($tag->isDirty('name') && ! $tag->isDirty('slug')) {
            $tag->slug = Str::slug($tag->name);
        }
    }

    public function updated(Tag $tag): void
    {
        Cache::forget('tags:all');
        Cache::forget('tags:list');
        Cache::forget("tags:{$tag->slug}");
    }

    public function deleted(Tag $tag): void
    {
        Cache::forget('tags:all');
        Cache::forget('tags:list');
        Cache::forget("tags:{$tag->slug}");
    }
}
