<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Permission;
use App\Models\UserPermission;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function hasPermission(string $permissionName): bool
    {
        \Log::debug("Checking permission: {$permissionName} for user: {$this->id}");
        
        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            \Log::debug("Permission not found in database: {$permissionName}");
            return false;
        }

        \Log::debug("Found permission ID: {$permission->id}");

        $hasPermission = UserPermission::where('user_id', $this->id)
            ->where('permission_id', $permission->id)
            ->exists();

        \Log::debug("User has permission: " . ($hasPermission ? 'true' : 'false'));
        
        return $hasPermission;
    }


}
