<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Create user-management permission
        $permission = Permission::create([
            'name' => 'user-management',
            'description' => 'Manage users and their permissions'
        ]);

        // Create admin user if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Assign permission to admin
        $user->permissions()->sync([$permission->id]);
    }
} 