<?php

namespace App\Domain\Creator;

use App\Domain\Creator\Contracts\CreatorRepositoryInterface;
use App\Domain\Creator\Contracts\CreatorServiceInterface;
use App\Domain\Creator\Events\CreatorCreated;
use App\Domain\Creator\Events\CreatorDeleted;
use App\Domain\Creator\Events\CreatorUpdated;
use App\Models\Creator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CreatorService implements CreatorServiceInterface
{
    public function __construct(protected CreatorRepositoryInterface $repository) {}

    public function listCreators(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = sprintf('creators:list:%s:%d', md5(json_encode($filters)), $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $this->repository->paginate($filters, $perPage)
        );
    }

    public function getCreator(Creator $creator): Creator
    {
        $cacheKey = sprintf('creators:%s', $creator->slug);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $creator->fresh(['region', 'stories.region']) ?? $creator
        );
    }

    public function createCreator(array $data): Creator
    {
        $creator = $this->repository->create($this->normalizePayload($data));

        event(new CreatorCreated($creator));

        return $creator;
    }

    public function updateCreator(Creator $creator, array $data): Creator
    {
        $creator = $this->repository->update($creator, $this->normalizePayload($data));

        event(new CreatorUpdated($creator));

        return $creator;
    }

    public function deleteCreator(Creator $creator): bool
    {
        $result = $this->repository->delete($creator);

        event(new CreatorDeleted($creator));

        return $result;
    }

    public function getCreatorStories(Creator $creator, int $limit = 10): Collection
    {
        return $this->repository->getStories($creator, $limit);
    }

    protected function normalizePayload(array $data): array
    {
        if (! empty($data['full_name']) && empty($data['name'])) {
            $data['name'] = $data['full_name'];
        }

        if (empty($data['full_name']) && ! empty($data['name'])) {
            $data['full_name'] = $data['name'];
        }

        if (! empty($data['specialties']) && is_array($data['specialties'])) {
            $data['specialties'] = array_values($data['specialties']);
        }

        if (! empty($data['languages']) && is_array($data['languages'])) {
            $data['languages'] = array_values($data['languages']);
        }

        if (! empty($data['social_links']) && is_array($data['social_links'])) {
            $data['social_links'] = array_values($data['social_links']);
        }

        return $data;
    }
}
