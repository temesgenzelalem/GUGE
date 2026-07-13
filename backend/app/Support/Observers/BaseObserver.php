<?php

namespace App\Support\Observers;

use Illuminate\Support\Facades\Cache;

abstract class BaseObserver
{
    protected function rememberCacheKey(string $key): string
    {
        return $key;
    }

    protected function invalidateCache(array|string $keys): void
    {
        foreach ((array) $keys as $key) {
            Cache::forget($key);
        }
    }
}
