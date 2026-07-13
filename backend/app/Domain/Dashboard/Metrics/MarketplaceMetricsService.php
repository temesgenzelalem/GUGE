<?php

namespace App\Domain\Dashboard\Metrics;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Category;
use App\Models\Product;

class MarketplaceMetricsService
{
    public function getMetrics(): array
    {
        return DashboardCache::remember(self::cacheKey(), function () {
            return [
                'total_products' => Product::count(),
                'total_categories' => Category::count(),
                'top_selling_products' => Product::query()
                    ->select('id', 'name', 'slug')
                    ->limit(3)
                    ->get()
                    ->map(fn ($product) => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'sales' => null,
                    ])
                    ->toArray(),
            ];
        });
    }

    public static function cacheKey(): string
    {
        return DashboardCache::MARKETPLACE;
    }
}
