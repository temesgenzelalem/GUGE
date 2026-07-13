<?php

namespace App\Domain\Product;

use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Domain\Product\Contracts\ProductServiceInterface;
use App\Domain\Product\Events\ProductCreated;
use App\Domain\Product\Events\ProductDeleted;
use App\Domain\Product\Events\ProductUpdated;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductService implements ProductServiceInterface
{
    public function __construct(protected ProductRepositoryInterface $repository) {}

    public function listProducts(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = sprintf('products:list:%s:%d', md5(json_encode($filters)), $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $this->repository->paginate($filters, $perPage)
        );
    }

    public function getProduct(Product $product): Product
    {
        $cacheKey = sprintf('products:%s', $product->slug);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $product->fresh(['region'])
        );
    }

    public function createProduct(array $data): Product
    {
        $product = $this->repository->create($data);

        event(new ProductCreated($product));

        return $product;
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $product = $this->repository->update($product, $data);

        event(new ProductUpdated($product));

        return $product;
    }

    public function deleteProduct(Product $product): bool
    {
        $result = $this->repository->delete($product);

        event(new ProductDeleted($product));

        return $result;
    }

    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return $this->repository->getRelated($product, $limit);
    }
}
