<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasRole('admin');
    }
}
