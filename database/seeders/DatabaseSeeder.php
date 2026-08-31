<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        Permission::factory()->createMany([
            [
                'name' => 'create permission',
                'ability' => 'create:permission',
                'category' => 'permissions',
            ],
            [
                'name' => 'read permission',
                'ability' => 'read:permission',
                'category' => 'permissions',
            ],
            [
                'name' => 'update permission',
                'ability' => 'update:permission',
                'category' => 'permissions',
            ],
            [
                'name' => 'delete permission',
                'ability' => 'delete:permission',
                'category' => 'permissions',
            ],
            [
                'name' => 'assign permission',
                'ability' => 'assign:permission',
                'category' => 'permissions',
            ],
            [
                'name' => 'revoke permission',
                'ability' => 'revoke:permission',
                'category' => 'permissions',
            ],
        ]);

        $admin = Admin::factory()->create([
            'name' => 'Toufique Al Ajay',
            'email' => 'toufique26ajay@gmail.com',
        ]);

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            DB::table('admin_permission')->insert([
                'admin_id' => $admin->id,
                'permission_id' => $permission->id,
            ]);
        }
    }
}
