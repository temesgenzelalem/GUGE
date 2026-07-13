<?php

namespace App\Domain\Graph\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface RegionGraphServiceInterface
{
    public function relatedNodes(int $regionId, array $types = []): Collection;

    public function connections(int $regionId, int $depth = 2): Collection;

    public function findRelatedRegions(string $query, array $filters = []): Collection;

    public function saveRelationship(array $attributes): void;
}
