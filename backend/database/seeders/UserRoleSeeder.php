<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $defaultRole = config('permissions.default_role');
        $users = User::all();

        foreach ($users as $user) {
            $normalizedRole = $this->normalizeLegacyRole($user->role ?? $defaultRole);

            if (! Role::where('name', $normalizedRole)->exists()) {
                $normalizedRole = $defaultRole;
            }

            if (! $user->hasSpatieRole($normalizedRole)) {
                $user->assignRole($normalizedRole);
            }
        }
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
}
