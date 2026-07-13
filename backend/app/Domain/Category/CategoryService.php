<?php

namespace App\Domain\Category;

use App\Domain\Category\Contracts\CategoryRepositoryInterface;
use App\Domain\Category\Contracts\CategoryServiceInterface;
use App\Domain\Category\Events\CategoryCreated;
use App\Domain\Category\Events\CategoryDeleted;
use App\Domain\Category\Events\CategoryUpdated;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(protected CategoryRepositoryInterface $repository) {}

    public function listCategories(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = sprintf('categories:list:%s:%d', md5(json_encode($filters)), $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $this->repository->paginate($filters, $perPage)
        );
    }

    public function getCategory(Category $category): Category
    {
        $cacheKey = sprintf('categories:%s', $category->slug);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $category->fresh()
        );
    }

    public function createCategory(array $data): Category
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = $this->repository->create($data);

        Cache::forget('categories:list');

        event(new CategoryCreated($category));

        return $category;
    }

    public function updateCategory(Category $category, array $data): Category
    {
        if (! empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $previousSlug = $category->slug;

        $category = $this->repository->update($category, $data);

        Cache::forget('categories:list');
        Cache::forget("categories:{$previousSlug}");
        Cache::forget("categories:{$category->slug}");

        event(new CategoryUpdated($category));

        return $category;
    }

    public function deleteCategory(Category $category): bool
    {
        $result = $this->repository->delete($category);

        event(new CategoryDeleted($category));

        return $result;
    }
}
