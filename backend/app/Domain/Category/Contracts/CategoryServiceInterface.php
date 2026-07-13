<?php

namespace App\Domain\Category\Contracts;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryServiceInterface
{
    public function listCategories(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getCategory(Category $category): Category;

    public function createCategory(array $data): Category;

    public function updateCategory(Category $category, array $data): Category;

    public function deleteCategory(Category $category): bool;
}
