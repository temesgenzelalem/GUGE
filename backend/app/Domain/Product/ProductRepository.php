<?php

namespace App\Domain\Product;

use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    public function query(array $filters = []): Builder
    {
        $query = Product::with('region');

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        if (! empty($filters['search'])) {
            $q = Str::lower(trim($filters['search']));
            $query->where(function (Builder $builder) use ($q) {
                $builder->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(story) LIKE ?', ["%{$q}%"]);
            });
        }

        return $query;
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->query($filters)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::where('slug', $slug)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getRelated(Product $product, int $limit = 4): Collection
    {
        return Product::with('region')
            ->where('id', '!=', $product->id)
            ->where(function (Builder $q) use ($product) {
                $q->where('region_id', $product->region_id)
                    ->orWhere('category', $product->category);
            })
            ->limit($limit)
            ->get();
    }
}
