<?php

namespace App\Support\Services;

use Illuminate\Contracts\Cache\Repository;

class CacheService
{
    public function __construct(protected Repository $cache) {}

    public function remember(string $key, \Closure $callback, int $ttl = 600): mixed
    {
        return $this->cache->remember($key, $ttl, $callback);
    }

    public function forget(string|array $keys): void
    {
        foreach ((array) $keys as $key) {
            $this->cache->forget($key);
        }
    }

    public function flush(): void
    {
        $this->cache->flush();
    }
}
