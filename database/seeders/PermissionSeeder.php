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
        $permissions = [
            'user-management' => 'Can access user management section',
            'view_users' => 'Can view users list and details',
            'create_users' => 'Can create new users', 
            'edit_users' => 'Can edit existing users',
            'delete_users' => 'Can delete users'
        ];

        foreach($permissions as $name => $description) {
            Permission::create([
                'name' => $name,
                'description' => $description
            ]);
        }

        // Then attach them to admin
        $user = User::where('email', 'admin@example.com')->first();
        $user->permissions()->attach(Permission::all());
    }
} 