<?php

namespace App\Domain\Dashboard;

use Closure;
use Illuminate\Support\Facades\Cache;

class DashboardCache
{
    public const USERS = 'admin:dashboard:users';

    public const REGIONS = 'admin:dashboard:regions';

    public const PRODUCTS = 'admin:dashboard:products';

    public const STORIES = 'admin:dashboard:stories';

    public const CREATORS = 'admin:dashboard:creators';

    public const MARKETPLACE = 'admin:dashboard:marketplace';

    public const TRAVEL = 'admin:dashboard:travel';

    public const ANALYTICS = 'admin:dashboard:analytics';

    public const SYSTEM = 'admin:dashboard:system';

    public static function remember(string $key, Closure $callback, int $minutes = 5): mixed
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    public static function forget(string $key): void
    {
        Cache::forget($key);
    }

    public static function forgetMany(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    public static function clearAll(): void
    {
        static::forgetMany([
            self::USERS,
            self::REGIONS,
            self::PRODUCTS,
            self::STORIES,
            self::CREATORS,
            self::MARKETPLACE,
            self::TRAVEL,
            self::ANALYTICS,
            self::SYSTEM,
        ]);
    }
}
