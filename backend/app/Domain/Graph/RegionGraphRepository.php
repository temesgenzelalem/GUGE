<?php

namespace App\Domain\Graph;

use App\Domain\Graph\Contracts\RegionGraphRepositoryInterface;
use App\Models\RegionRelationship;
use Illuminate\Database\Eloquent\Collection;

class RegionGraphRepository implements RegionGraphRepositoryInterface
{
    public function getRelatedNodes(int $regionId, array $types = []): Collection
    {
        $query = RegionRelationship::with(['source', 'target'])
            ->where('source_region_id', $regionId);

        if (! empty($types)) {
            $query->whereIn('target_type', $types);
        }

        return $query->get();
    }

    public function getConnections(int $regionId, int $depth = 2): Collection
    {
        return RegionRelationship::with(['source', 'target'])
            ->where('source_region_id', $regionId)
            ->limit($depth * 10)
            ->get();
    }

    public function findRegionsByContent(string $query, array $filters = []): Collection
    {
        return RegionRelationship::with(['source', 'target'])
            ->where('target_type', 'region')
            ->where('target_name', 'ILIKE', "%{$query}%")
            ->limit(20)
            ->get();
    }

    public function saveRelationship(array $attributes): void
    {
        RegionRelationship::updateOrCreate(
            [
                'source_region_id' => $attributes['source_region_id'],
                'target_type' => $attributes['target_type'],
                'target_id' => $attributes['target_id'],
            ],
            $attributes
        );
    }
}
