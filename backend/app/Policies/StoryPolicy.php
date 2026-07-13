<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\User;

class StoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(?User $user, Story $story): bool
    {
        if ($story->status === 'published') {
            return true;
        }

        return $user?->hasRole(['admin', 'moderator'])
            || ($user?->hasRole('creator') && $story->creator_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'moderator', 'creator']);
    }

    public function update(User $user, Story $story): bool
    {
        return $user->hasRole(['admin', 'moderator']) || ($user->hasRole('creator') && $story->creator_id === $user->id);
    }

    public function delete(User $user, Story $story): bool
    {
        return $user->hasRole(['admin', 'moderator']) || ($user->hasRole('creator') && $story->creator_id === $user->id);
    }
}
