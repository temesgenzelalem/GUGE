<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Authorization — must run first
            PermissionSeeder::class,
            RoleSeeder::class,
            UserRoleSeeder::class,

            // Core domain data — order matters (regions before dependents)
            RegionSeeder::class,
            CategorySeeder::class,
            CreatorSeeder::class,
            ProductSeeder::class,
            StorySeeder::class,
        ]);
    }
}
