<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserPermission;

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
            $permission = Permission::create([
                'name' => $name,
                'description' => $description
            ]);

            UserPermission::create([
                'user_id' => User::where('email', 'admin@example.com')->first()->id,
                'permission_id' => $permission->id
            ]);
        }

        
    }
} 