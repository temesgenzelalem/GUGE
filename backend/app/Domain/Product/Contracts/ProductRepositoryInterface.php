<?php

namespace App\Domain\Product\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function query(array $filters = []): Builder;

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): bool;

    public function getRelated(Product $product, int $limit = 4): Collection;
}
