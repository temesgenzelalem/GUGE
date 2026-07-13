<?php

namespace App\Domain\Graph;

use App\Domain\Graph\Contracts\RegionGraphRepositoryInterface;
use App\Domain\Graph\Contracts\RegionGraphServiceInterface;
use App\Support\Services\CacheService;
use Illuminate\Database\Eloquent\Collection;

class RegionGraphService implements RegionGraphServiceInterface
{
    public function __construct(
        protected RegionGraphRepositoryInterface $repository,
        protected CacheService $cache,
    ) {}

    public function relatedNodes(int $regionId, array $types = []): Collection
    {
        $cacheKey = sprintf('graph:related:%d:%s', $regionId, implode(',', $types));

        return $this->cache->remember($cacheKey, fn () => $this->repository->getRelatedNodes($regionId, $types));
    }

    public function connections(int $regionId, int $depth = 2): Collection
    {
        $cacheKey = sprintf('graph:connections:%d:%d', $regionId, $depth);

        return $this->cache->remember($cacheKey, fn () => $this->repository->getConnections($regionId, $depth));
    }

    public function findRelatedRegions(string $query, array $filters = []): Collection
    {
        $cacheKey = sprintf('graph:regions:search:%s', md5($query.json_encode($filters)));

        return $this->cache->remember($cacheKey, fn () => $this->repository->findRegionsByContent($query, $filters));
    }

    public function saveRelationship(array $attributes): void
    {
        $this->repository->saveRelationship($attributes);
        $this->cache->forget(['graph:related:'.$attributes['source_region_id'], 'graph:connections:'.$attributes['source_region_id']]);
    }
}
