<?php

namespace App\Domain\Media;

use App\Domain\Media\Contracts\MediaRepositoryInterface;
use App\Domain\Media\Contracts\MediaServiceInterface;
use App\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class MediaService implements MediaServiceInterface
{
    public function __construct(protected MediaRepositoryInterface $repository) {}

    public function listMedia(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = sprintf('media:list:%s:%d', md5(json_encode($filters)), $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(10), fn () => $this->repository->paginate($filters, $perPage)
        );
    }

    public function getMedia(Media $media): Media
    {
        return $media->fresh();
    }

    public function uploadMedia(array $data): Media
    {
        $media = $this->repository->create($data);
        Cache::forget('media:list');

        return $media;
    }

    public function updateMedia(Media $media, array $data): Media
    {
        $media = $this->repository->update($media, $data);
        Cache::forget('media:list');

        return $media;
    }

    public function deleteMedia(Media $media): bool
    {
        $result = $this->repository->delete($media);
        Cache::forget('media:list');

        return $result;
    }
}
