<?php

namespace App\Domain\Story\Contracts;

use App\Models\Story;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface StoryServiceInterface
{
    public function listStories(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getStory(Story $story): Story;

    public function createStory(array $data): Story;

    public function updateStory(Story $story, array $data): Story;

    public function deleteStory(Story $story): bool;

    public function getRelatedStories(Story $story, int $limit = 3): Collection;

    public function incrementViewCount(Story $story): void;
}
