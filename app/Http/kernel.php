<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        //
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
           //
            \App\Http\Middleware\ActivityLogger::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            \App\Http\Middleware\ActivityLogger::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        //
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        //
    ];

}
