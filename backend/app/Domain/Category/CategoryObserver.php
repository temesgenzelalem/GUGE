<?php

namespace App\Domain\Category;

use App\Domain\Category\Events\CategoryCreated;
use App\Domain\Category\Events\CategoryDeleted;
use App\Domain\Category\Events\CategoryUpdated;
use App\Domain\Dashboard\DashboardCache;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function creating(Category $category): void
    {
        if (empty($category->slug)) {
            $category->slug = Str::slug($category->name);
        }
    }

    public function created(Category $category): void
    {
        Cache::forget('categories:list');
        DashboardCache::clearAll();
        event(new CategoryCreated($category));
    }

    public function updating(Category $category): void
    {
        if ($category->isDirty('name') && ! $category->isDirty('slug')) {
            $category->slug = Str::slug($category->name);
        }
    }

    public function updated(Category $category): void
    {
        Cache::forget('categories:list');
        Cache::forget(sprintf('categories:%s', $category->slug));
        DashboardCache::clearAll();
        event(new CategoryUpdated($category));
    }

    public function deleted(Category $category): void
    {
        Cache::forget('categories:list');
        Cache::forget(sprintf('categories:%s', $category->slug));
        DashboardCache::clearAll();
        event(new CategoryDeleted($category));
    }
}
