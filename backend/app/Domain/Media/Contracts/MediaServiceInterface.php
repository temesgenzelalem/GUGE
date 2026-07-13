<?php

namespace App\Domain\Media\Contracts;

use App\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;

interface MediaServiceInterface
{
    public function listMedia(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getMedia(Media $media): Media;

    public function uploadMedia(array $data): Media;

    public function updateMedia(Media $media, array $data): Media;

    public function deleteMedia(Media $media): bool;
}
