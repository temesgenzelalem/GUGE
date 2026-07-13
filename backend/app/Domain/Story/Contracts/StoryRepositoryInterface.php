<?php

namespace App\Domain\Story\Contracts;

use App\Models\Story;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface StoryRepositoryInterface
{
    public function query(array $filters = []): Builder;

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Story;

    public function create(array $data): Story;

    public function update(Story $story, array $data): Story;

    public function delete(Story $story): bool;

    public function getRelated(Story $story, int $limit = 3): Collection;
}
