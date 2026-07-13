<?php

namespace App\Domain\Product\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function listProducts(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getProduct(Product $product): Product;

    public function createProduct(array $data): Product;

    public function updateProduct(Product $product, array $data): Product;

    public function deleteProduct(Product $product): bool;

    public function getRelatedProducts(Product $product, int $limit = 4): Collection;
}
