<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //EventServiceProvider.php
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->id == 1 ? true : null;
        });

        if (app()->environment('local')) {
            Config::set('app.url', 'http://127.0.0.1:8000');
        } else {
            Config::set('app.url', 'https://api.myfreeadmission.com');
        }
    }
}
