<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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
        $this->registerPolicies();

        Gate::define('user-management', function (User $user) {
            Log::debug("Gate checking user-management for user: {$user->id}");
            $result = $user->hasPermission('user-management');
            Log::debug("Gate result: " . ($result ? 'true' : 'false'));
            return $result;
        });
    }
} 