<?php

namespace App\Domain\Region;

use App\Domain\Region\Contracts\RegionRepositoryInterface;
use App\Models\Creator;
use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RegionRepository implements RegionRepositoryInterface
{
    public function query(array $filters = []): Builder
    {
        $query = Region::query();

        if (! empty($filters['direction'])) {
            $query->where('direction', $filters['direction']);
        }

        if (! empty($filters['search'])) {
            $q = trim($filters['search']);
            $query->where(function (Builder $builder) use ($q) {
                $builder->whereRaw('name ILIKE ?', ["%{$q}%"])
                    ->orWhereRaw('description ILIKE ?', ["%{$q}%"])
                    ->orWhereRaw('zone ILIKE ?', ["%{$q}%"]);
            });
        }

        return $query;
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->query($filters)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Region
    {
        return Region::where('slug', $slug)->first();
    }

    public function create(array $data): Region
    {
        return Region::create($data);
    }

    public function update(Region $region, array $data): Region
    {
        $region->update($data);

        return $region;
    }

    public function delete(Region $region): bool
    {
        return $region->delete();
    }

    public function products(Region $region): Collection
    {
        return $region->products()
            ->with('region')
            ->orderBy('name')
            ->get();
    }

    public function stories(Region $region): Collection
    {
        return $region->stories()
            ->with(['region', 'creator'])
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function creators(Region $region): Collection
    {
        return Creator::where('region_id', $region->id)
            ->with('region')
            ->orderByDesc('rating')
            ->get();
    }
}
