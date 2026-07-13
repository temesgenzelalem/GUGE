<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $guardName = config('auth.defaults.guard', 'web');
        $roles = array_keys(config('permissions.roles', []));

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => $guardName,
            ]);

            $permissions = config('permissions.roles.'.$roleName.'.permissions', []);

            if (in_array('*', $permissions, true)) {
                $permissions = array_unique(config('permissions.permissions', []));
            }

            if (! empty($permissions)) {
                $permissionModels = Permission::whereIn('name', $permissions)
                    ->where('guard_name', $guardName)
                    ->get();

                $role->syncPermissions($permissionModels);
            }
        }
    }
}
