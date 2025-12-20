<?php

namespace Database\Seeders;

use App\Http\Controllers\RoleController;
use App\Models\Manager;
use App\Models\Role;
use App\Models\User;
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
        Manager::query()->create([
            'name' => 'admin',
            'password' => 'password',
        ]);
        Role::query()->create([
            'title' => 'admin',
            'abilities' => ["*"],
            'branch' => 'admin',
        ]);
        Role::query()->create([
            'title' => "Base",
            'abilities' => ['task-index', 'task-create'],
            'master_id' => 1,
            'branch' => 'admin->Base',
            'depth' => 1
        ]);
        User::query()->create([
            'name' => 'admin',
            'password' => 'password',
            'role_id' => 1,
        ]);
        User::query()->create([
            'name' => 'user',
            'password' => 'password',
            'role_id' => 2,
        ]);
    }
}
