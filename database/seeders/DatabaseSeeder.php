<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // 1. Create the roles first
        $this->call(RolePermissionSeeder::class);

        // 2. Create users and assign the roles
        $this->call(AssignRoleSeeder::class);
    }
}
