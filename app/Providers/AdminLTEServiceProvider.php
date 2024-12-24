<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AdminLTEServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../vendor/jeroennoten/laravel-adminlte/resources/views', 'adminlte');
    }
} 