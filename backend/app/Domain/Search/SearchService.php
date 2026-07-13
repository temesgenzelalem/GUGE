<?php

namespace App\Domain\Search;

use App\Domain\Search\Contracts\SearchServiceInterface;
use App\Models\Creator;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use Illuminate\Support\Str;

class SearchService implements SearchServiceInterface
{
    public function search(string $query, array $filters = []): array
    {
        $q = Str::lower(trim($query));
        $like = "%{$q}%";

        $regions = Region::where(function ($builder) use ($like) {
            $builder->whereRaw('LOWER(name) LIKE ?', [$like])
                ->orWhereRaw('LOWER(description) LIKE ?', [$like])
                ->orWhereRaw('LOWER(zone) LIKE ?', [$like]);
        })->limit(5)->get();

        $products = Product::with('region')
            ->where(function ($builder) use ($like) {
                $builder->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$like]);
            })->limit(5)->get();

        $stories = Story::with(['region', 'creator', 'tags'])
            ->where('status', 'published')
            ->where(function ($builder) use ($like) {
                $builder->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(content) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(category) LIKE ?', [$like])
                    ->orWhereHas('region', fn ($b) => $b->whereRaw('LOWER(name) LIKE ?', [$like]))
                    ->orWhereHas('creator', fn ($b) => $b->whereRaw('LOWER(full_name) LIKE ?', [$like]))
                    ->orWhereHas('tags', fn ($b) => $b->whereRaw('LOWER(name) LIKE ?', [$like]));
            })->limit(5)->get();

        $creators = Creator::with('region')
            ->where(function ($builder) use ($like) {
                $builder->whereRaw('LOWER(full_name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(username) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(bio) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(role) LIKE ?', [$like]);
            })->limit(5)->get();

        return [
            'regions' => $regions,
            'products' => $products,
            'stories' => $stories,
            'creators' => $creators,
        ];
    }
}
