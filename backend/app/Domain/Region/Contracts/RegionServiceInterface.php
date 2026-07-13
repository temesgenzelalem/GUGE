<?php

namespace App\Domain\Region\Contracts;

use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RegionServiceInterface
{
    public function listRegions(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getRegion(Region $region): Region;

    public function createRegion(array $data): Region;

    public function updateRegion(Region $region, array $data): Region;

    public function deleteRegion(Region $region): bool;

    public function getRegionProducts(Region $region): Collection;

    public function getRegionStories(Region $region): Collection;

    public function getRegionCreators(Region $region): Collection;
}
