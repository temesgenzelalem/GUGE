<?php

namespace App\Support\Authorization;

use Spatie\Permission\Traits\HasRoles as SpatieHasRoles;

trait HasPermissions
{
    use SpatieHasRoles {
        hasRole as protected spatieHasRole;
        hasAnyRole as protected spatieHasAnyRole;
        hasAllRoles as protected spatieHasAllRoles;
        hasExactRoles as protected spatieHasExactRoles;
        hasPermissionTo as protected spatieHasPermissionTo;
        checkPermissionTo as protected spatieCheckPermissionTo;
    }

    public function roleName(): string
    {
        return $this->normalizeLegacyRole($this->role ?? config('permissions.default_role'));
    }

    protected function normalizeLegacyRole(string $role): string
    {
        $role = strtolower(trim($role));

        return match ($role) {
            'user' => config('permissions.default_role'),
            'tour guide', 'tour-guide' => 'tour_guide',
            '' => config('permissions.default_role'),
            default => $role,
        };
    }

    public function legacyPermissions(): array
    {
        if ($this->role !== null) {
            return config('permissions.roles.'.$this->roleName().'.permissions', []);
        }

        if ($this->roles()->count() === 0) {
            return config('permissions.roles.'.config('permissions.default_role').'.permissions', []);
        }

        return [];
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        if ($this->role !== null && in_array(strtolower($this->roleName()), array_map('strtolower', $roles), true)) {
            return true;
        }

        if ($this->spatieHasRole($roles)) {
            return true;
        }

        if ($this->role === null && $this->roles()->count() === 0) {
            return in_array(strtolower(config('permissions.default_role')), array_map('strtolower', $roles), true);
        }

        return false;
    }

    public function hasSpatieRole(string|array $roles): bool
    {
        return $this->spatieHasRole($roles);
    }

    public function hasSpatiePermission(string $permission): bool
    {
        return $this->spatieCheckPermissionTo($permission);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->legacyPermissions();

        if (in_array('*', $permissions, true)) {
            return true;
        }

        foreach ($permissions as $allowed) {
            if ($allowed === $permission) {
                return true;
            }

            if (str_ends_with($allowed, '*') && str_starts_with($permission, rtrim($allowed, '*'))) {
                return true;
            }
        }

        return $this->spatieCheckPermissionTo($permission);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin']);
    }
}
