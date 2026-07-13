<?php

namespace App\Domain\User;

use App\Domain\Dashboard\DashboardCache;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    public function created(User $user): void
    {
        Cache::forget('users:list');
        DashboardCache::clearAll();
    }

    public function updated(User $user): void
    {
        Cache::forget('users:list');
        DashboardCache::clearAll();
    }

    public function deleted(User $user): void
    {
        Cache::forget('users:list');
        DashboardCache::clearAll();
    }
}
