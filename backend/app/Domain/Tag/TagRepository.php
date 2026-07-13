<?php

namespace App\Domain\Tag;

use App\Domain\Tag\Contracts\TagRepositoryInterface;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TagRepository implements TagRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        return $this->applyFilters(Tag::query(), $filters)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return Tag::orderBy('name')->get();
    }

    public function findBySlug(string $slug): ?Tag
    {
        return Tag::where('slug', $slug)->first();
    }

    public function findOrCreateByName(string $name): Tag
    {
        $slug = Str::slug($name);

        return Tag::firstOrCreate(
            ['slug' => $slug],
            ['name' => ucfirst(trim($name))]
        );
    }

    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): Tag
    {
        $tag->update($data);

        return $tag;
    }

    public function delete(Tag $tag): bool
    {
        return (bool) $tag->delete();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $q = strtolower(trim($filters['search']));
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"]);
        }

        return $query;
    }
}
