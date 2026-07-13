<?php

namespace App\Domain\Media;

use App\Domain\Media\Contracts\MediaRepositoryInterface;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class MediaRepository implements MediaRepositoryInterface
{
    public function query(array $filters = []): Builder
    {
        $query = Media::query();

        if (! empty($filters['search'])) {
            $q = trim($filters['search']);
            $query->where(function (Builder $builder) use ($q) {
                $builder->where('filename', 'ILIKE', "%{$q}%")
                    ->orWhere('path', 'ILIKE', "%{$q}%")
                    ->orWhere('mime_type', 'ILIKE', "%{$q}%");
            });
        }

        if (isset($filters['gallery'])) {
            $query->where('gallery', (bool) $filters['gallery']);
        }

        return $query;
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->query($filters)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Media
    {
        return Media::where('uuid', $uuid)->first();
    }

    public function create(array $data): Media
    {
        return Media::create($data);
    }

    public function update(Media $media, array $data): Media
    {
        $media->update($data);

        return $media;
    }

    public function delete(Media $media): bool
    {
        return $media->delete();
    }
}
