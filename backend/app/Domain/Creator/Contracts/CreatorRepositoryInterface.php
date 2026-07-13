<?php

namespace App\Domain\Creator\Contracts;

use App\Models\Creator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CreatorRepositoryInterface
{
    public function query(array $filters = []): Builder;

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Creator;

    public function create(array $data): Creator;

    public function update(Creator $creator, array $data): Creator;

    public function delete(Creator $creator): bool;

    public function getStories(Creator $creator, int $limit = 10): Collection;
}
