<?php

namespace App\Domain\Creator\Contracts;

use App\Models\Creator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CreatorServiceInterface
{
    public function listCreators(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getCreator(Creator $creator): Creator;

    public function createCreator(array $data): Creator;

    public function updateCreator(Creator $creator, array $data): Creator;

    public function deleteCreator(Creator $creator): bool;

    public function getCreatorStories(Creator $creator, int $limit = 10): Collection;
}
