<?php

namespace App\Support\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

abstract class AbstractService
{
    public function __construct(protected CacheRepository $cache) {}

    protected function cacheRemember(string $key, \Closure $callback, int $ttl = 600): mixed
    {
        return $this->cache->remember($key, $ttl, $callback);
    }

    protected function cacheForget(string|array $keys): void
    {
        foreach ((array) $keys as $key) {
            $this->cache->forget($key);
        }
    }
}
