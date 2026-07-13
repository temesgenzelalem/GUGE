<?php

namespace App\Domain\Tag\Contracts;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TagServiceInterface
{
    public function listTags(array $filters = [], int $perPage = 50): LengthAwarePaginator;

    public function allTags(): Collection;

    public function getTag(Tag $tag): Tag;

    public function createTag(array $data): Tag;

    public function updateTag(Tag $tag, array $data): Tag;

    public function deleteTag(Tag $tag): bool;
}
