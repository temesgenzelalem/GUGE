<?php

namespace App\Domain\Region\Contracts;

use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RegionRepositoryInterface
{
    public function query(array $filters = []): Builder;

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Region;

    public function create(array $data): Region;

    public function update(Region $region, array $data): Region;

    public function delete(Region $region): bool;

    public function products(Region $region): Collection;

    public function stories(Region $region): Collection;

    public function creators(Region $region): Collection;
}
