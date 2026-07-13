<?php

namespace App\Domain\Creator;

use App\Domain\Creator\Contracts\CreatorRepositoryInterface;
use App\Models\Creator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CreatorRepository implements CreatorRepositoryInterface
{
    public function query(array $filters = []): Builder
    {
        $query = Creator::query();

        if (! empty($filters['search'])) {
            $q = Str::lower(trim($filters['search']));
            $query->where(function (Builder $builder) use ($q) {
                $builder->whereRaw('LOWER(full_name) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(username) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(name) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(bio) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(role) LIKE ?', ["%{$q}%"]);
            });
        }

        if (! empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        if (! empty($filters['specialties'])) {
            $query->whereJsonContains('specialties', $filters['specialties']);
        }

        if (! empty($filters['languages'])) {
            $query->whereJsonContains('languages', $filters['languages']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->query($filters)
            ->with(['region', 'stories'])
            ->orderByDesc('rating')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Creator
    {
        return Creator::with(['region', 'stories'])
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data): Creator
    {
        return Creator::create($data);
    }

    public function update(Creator $creator, array $data): Creator
    {
        $creator->update($data);

        return $creator;
    }

    public function delete(Creator $creator): bool
    {
        return $creator->delete();
    }

    public function getStories(Creator $creator, int $limit = 10): Collection
    {
        return $creator->stories()
            ->with('region')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
