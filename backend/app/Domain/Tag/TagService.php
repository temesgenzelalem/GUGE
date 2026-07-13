<?php

namespace App\Domain\Tag;

use App\Domain\Tag\Contracts\TagRepositoryInterface;
use App\Domain\Tag\Contracts\TagServiceInterface;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TagService implements TagServiceInterface
{
    public function __construct(protected TagRepositoryInterface $repository) {}

    public function listTags(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $cacheKey = sprintf('tags:list:%s:%d', md5(json_encode($filters)), $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(30), fn () => $this->repository->paginate($filters, $perPage)
        );
    }

    public function allTags(): Collection
    {
        return Cache::remember('tags:all', now()->addMinutes(30), fn () => $this->repository->all()
        );
    }

    public function getTag(Tag $tag): Tag
    {
        return Cache::remember("tags:{$tag->slug}", now()->addMinutes(30), fn () => $tag->fresh(['stories']) ?? $tag
        );
    }

    public function createTag(array $data): Tag
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $tag = $this->repository->create($data);

        Cache::forget('tags:all');
        Cache::forget('tags:list');

        return $tag;
    }

    public function updateTag(Tag $tag, array $data): Tag
    {
        if (! empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $tag = $this->repository->update($tag, $data);

        Cache::forget('tags:all');
        Cache::forget('tags:list');
        Cache::forget("tags:{$tag->slug}");

        return $tag;
    }

    public function deleteTag(Tag $tag): bool
    {
        $slug = $tag->slug;
        $result = $this->repository->delete($tag);

        Cache::forget('tags:all');
        Cache::forget('tags:list');
        Cache::forget("tags:{$slug}");

        return $result;
    }
}
