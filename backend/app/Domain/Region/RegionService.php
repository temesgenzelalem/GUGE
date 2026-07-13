<?php

namespace App\Domain\Region;

use App\Domain\Region\Contracts\RegionRepositoryInterface;
use App\Domain\Region\Contracts\RegionServiceInterface;
use App\Domain\Region\Events\RegionCreated;
use App\Domain\Region\Events\RegionDeleted;
use App\Domain\Region\Events\RegionUpdated;
use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class RegionService implements RegionServiceInterface
{
    public function __construct(protected RegionRepositoryInterface $repository) {}

    public function listRegions(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = sprintf('regions:list:%s:%d', md5(json_encode($filters)), $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $this->repository->paginate($filters, $perPage)
        );
    }

    public function getRegion(Region $region): Region
    {
        $cacheKey = sprintf('regions:%s', $region->slug);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $region->fresh()
        );
    }

    public function createRegion(array $data): Region
    {
        $region = $this->repository->create($data);

        event(new RegionCreated($region));

        return $region;
    }

    public function updateRegion(Region $region, array $data): Region
    {
        $region = $this->repository->update($region, $data);

        event(new RegionUpdated($region));

        return $region;
    }

    public function deleteRegion(Region $region): bool
    {
        $result = $this->repository->delete($region);

        event(new RegionDeleted($region));

        return $result;
    }

    public function getRegionProducts(Region $region): Collection
    {
        return $this->repository->products($region);
    }

    public function getRegionStories(Region $region): Collection
    {
        return $this->repository->stories($region);
    }

    public function getRegionCreators(Region $region): Collection
    {
        return $this->repository->creators($region);
    }
}
