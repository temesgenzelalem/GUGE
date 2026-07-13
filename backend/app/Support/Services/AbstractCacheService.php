<?php

namespace App\Support\Services;

use Illuminate\Contracts\Cache\Repository;

abstract class AbstractCacheService
{
    public function __construct(protected Repository $cache) {}

    public function remember(string $key, \Closure $callback, int $ttl = 600): mixed
    {
        return $this->cache->remember($key, $ttl, $callback);
    }

    public function forget(string|array $key): void
    {
        foreach ((array) $key as $item) {
            $this->cache->forget($item);
        }
    }
}
