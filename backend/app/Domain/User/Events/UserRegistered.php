<?php

namespace App\Domain\User\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use SerializesModels;

    public function __construct(public User $user) {}
}
