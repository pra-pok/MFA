<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogAuthenticatedUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Authenticated $event): void
    {
        $user = $event->user;
        logUserAction(
            $user->id,
            $user->team_id,
            'User Logged In',
            [
                'user_name' => $user->name,
                'email' => $user->email,
                'timestamp' => now(),
            ]
        );
    }

}
