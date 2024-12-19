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
            'description' => 'Can manage users and their permissions'
        ]);

        // Get admin user
        $user = User::where('email', 'admin@example.com')->first();
        
        // Attach permission to admin
        $user->permissions()->attach($permission);
    }
} 