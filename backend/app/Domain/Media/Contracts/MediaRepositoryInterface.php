<?php

namespace App\Domain\Media\Contracts;

use App\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;

interface MediaRepositoryInterface
{
    public function query(array $filters = []);

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findByUuid(string $uuid): ?Media;

    public function create(array $data): Media;

    public function update(Media $media, array $data): Media;

    public function delete(Media $media): bool;
}
