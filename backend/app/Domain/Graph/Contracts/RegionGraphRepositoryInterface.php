<?php

namespace App\Domain\Graph\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface RegionGraphRepositoryInterface
{
    public function getRelatedNodes(int $regionId, array $types = []): Collection;

    public function getConnections(int $regionId, int $depth = 2): Collection;

    public function findRegionsByContent(string $query, array $filters = []): Collection;

    public function saveRelationship(array $attributes): void;
}
