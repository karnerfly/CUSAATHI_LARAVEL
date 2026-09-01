<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
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
        Permission::upsert(
            [
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
            ],
            ['ability'],
            ['name', 'category'],
        );

        $admin = Admin::updateOrCreate(
            ['email' => 'toufique26ajay@gmail.com'],
            [
                'name' => 'Toufique Al Ajay',
            ],
        );

        $permissions = Permission::all();

        $adminPermissions = $permissions
            ->map(
                fn($permission) => [
                    'admin_id' => $admin->id,
                    'permission_id' => $permission->id,
                ],
            )
            ->all();

        DB::table('admin_permission')->upsert($adminPermissions, ['admin_id', 'permission_id'], []);
    }
}
