<?php

namespace App\Providers;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Authenticated::class => [
            'App\Listeners\LogAuthenticated',
        ],
        ];


    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
