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
            ],
            [
                'name' => 'read permission',
                'ability' => 'read:permission',
            ],
            [
                'name' => 'update permission',
                'ability' => 'update:permission',
            ],
            [
                'name' => 'delete permission',
                'ability' => 'delete:permission',
            ],
            [
                'name' => 'assign permission',
                'ability' => 'assign:permission',
            ],
            [
                'name' => 'revoke permission',
                'ability' => 'revoke:permission',
            ],
        ]);

        Admin::factory()->create([
            'name' => 'Toufique Al Ajay',
            'email' => 'toufique26ajay@gmail.com',
        ]);

        $admin = Admin::where('email', 'toufique26ajay@gmail.com')->first();
        $permissions = Permission::all();

        if ($admin) {
            foreach ($permissions as $permission) {
                DB::table('admin_permission')->insert([
                    'admin_id' => $admin->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }
    }
}
