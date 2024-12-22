<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('user-management', function ($user) {
            \Log::info('Checking user-management permission for user: ' . $user->email);
            \Log::info('User has permissions: ' . $user->permissions->pluck('name'));
            return $user->hasPermission('user-management');
        });
    }
} 