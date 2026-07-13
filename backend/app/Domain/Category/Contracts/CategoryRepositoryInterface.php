<?php

namespace App\Domain\Category\Contracts;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function query(array $filters = []);

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Category;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): bool;
}
