<?php

namespace App\Policies;

use App\Models\Creator;
use App\Models\User;

class CreatorPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Creator $creator): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Creator $creator): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Creator $creator): bool
    {
        return $user->isAdmin();
    }
}
