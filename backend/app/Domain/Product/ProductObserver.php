<?php

namespace App\Domain\Product;

use App\Domain\Dashboard\DashboardCache;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductObserver
{
    public function creating(Product $product): void
    {
        if (empty($product->slug)) {
            $product->slug = Str::slug($product->name);
        }
    }

    public function created(Product $product): void
    {
        Cache::forget('products:list');
        DashboardCache::clearAll();
    }

    public function updating(Product $product): void
    {
        if ($product->isDirty('name') && ! $product->isDirty('slug')) {
            $product->slug = Str::slug($product->name);
        }
    }

    public function updated(Product $product): void
    {
        Cache::forget("products:{$product->slug}");
        Cache::forget('products:list');
        DashboardCache::clearAll();
    }

    public function deleted(Product $product): void
    {
        Cache::forget("products:{$product->slug}");
        Cache::forget('products:list');
        DashboardCache::clearAll();
    }
}
