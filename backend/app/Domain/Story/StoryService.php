<?php

namespace App\Domain\Story;

use App\Domain\Story\Contracts\StoryRepositoryInterface;
use App\Domain\Story\Contracts\StoryServiceInterface;
use App\Domain\Story\Events\StoryCreated;
use App\Domain\Story\Events\StoryDeleted;
use App\Domain\Story\Events\StoryUpdated;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StoryService implements StoryServiceInterface
{
    public function __construct(protected StoryRepositoryInterface $repository) {}

    public function listStories(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        if (empty($filters['status'])) {
            $user = auth()->user();
            if (! $user || ! $user->hasRole(['admin', 'moderator', 'creator'])) {
                $filters['status'] = 'published';
            }
        }

        $cacheKey = sprintf('stories:list:%s:%d', md5(json_encode($filters)), $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $this->repository->paginate($filters, $perPage)
        );
    }

    public function getStory(Story $story): Story
    {
        $cacheKey = sprintf('stories:%s', $story->slug);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $story->fresh(['region', 'creator', 'tags', 'products']) ?? $story
        );
    }

    public function createStory(array $data): Story
    {
        $story = $this->repository->create($this->normalizePayload($data));
        $this->syncRelations($story, $data);

        event(new StoryCreated($story));

        return $story;
    }

    public function updateStory(Story $story, array $data): Story
    {
        $story = $this->repository->update($story, $this->normalizePayload($data));
        $this->syncRelations($story, $data);

        event(new StoryUpdated($story));

        return $story;
    }

    public function deleteStory(Story $story): bool
    {
        $result = $this->repository->delete($story);

        event(new StoryDeleted($story));

        return $result;
    }

    public function getRelatedStories(Story $story, int $limit = 3): Collection
    {
        return $this->repository->getRelated($story, $limit);
    }

    public function incrementViewCount(Story $story): void
    {
        $story->increment('view_count');
        Cache::forget(sprintf('stories:%s', $story->slug));
    }

    protected function normalizePayload(array $data): array
    {
        if (empty($data['content']) && ! empty($data['body'])) {
            $data['content'] = $data['body'];
        }

        if (empty($data['category']) && ! empty($data['type'])) {
            $data['category'] = $data['type'];
        }

        if (! empty($data['gallery']) && is_array($data['gallery'])) {
            $data['gallery'] = array_values($data['gallery']);
        }

        return $data;
    }

    protected function syncRelations(Story $story, array $data): void
    {
        if (isset($data['tags'])) {
            $tagIds = collect($data['tags'])->map(function ($tag) {
                $slug = Str::slug((string) $tag);

                return Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => is_string($tag) ? ucfirst($tag) : $slug]
                )->id;
            })->toArray();

            $story->tags()->sync($tagIds);
        }

        if (! empty($data['product_ids'])) {
            $story->products()->sync($data['product_ids']);
        }
    }
}
