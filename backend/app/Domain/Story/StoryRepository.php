<?php

namespace App\Domain\Story;

use App\Domain\Story\Contracts\StoryRepositoryInterface;
use App\Models\Story;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class StoryRepository implements StoryRepositoryInterface
{
    public function query(array $filters = []): Builder
    {
        $query = Story::with(['region', 'creator', 'tags', 'products']);

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        if (! empty($filters['creator_id'])) {
            $query->where('creator_id', $filters['creator_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $q = Str::lower(trim($filters['search']));
            $query->where(function (Builder $builder) use ($q) {
                $builder->whereRaw('LOWER(title) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(content) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(category) LIKE ?', ["%{$q}%"])
                    ->orWhereHas('region', fn (Builder $builder) => $builder->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"]))
                    ->orWhereHas('creator', fn (Builder $builder) => $builder->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"]))
                    ->orWhereHas('tags', fn (Builder $builder) => $builder->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"]));
            });
        }

        return $query;
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->query($filters)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Story
    {
        return Story::with(['region', 'creator', 'tags', 'products'])
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data): Story
    {
        return Story::create($data);
    }

    public function update(Story $story, array $data): Story
    {
        $story->update($data);

        return $story;
    }

    public function delete(Story $story): bool
    {
        return $story->delete();
    }

    public function getRelated(Story $story, int $limit = 3): Collection
    {
        return Story::with(['region', 'creator', 'tags', 'products'])
            ->where('id', '!=', $story->id)
            ->where(function (Builder $query) use ($story) {
                $query->where('region_id', $story->region_id)
                    ->orWhere('category', $story->category);
            })
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
