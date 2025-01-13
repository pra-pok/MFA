<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $url = $request->url();
        $method = $request->method();

        // Build the log message
        $message = "User Activity: ";

        if ($user) {
            // Log the user activity using custom logUserAction
            logUserAction(
                $user->id,  // User ID
                $user->team_id,  // Team ID
                'User Activity Recorded',
                [
                    'action' => $method,
                    'url' => $url,
                    'timestamp' => now(),
                ]
            );

            // Add user-specific data to the message
            $message .= "User ID: {$user->name}, ";
        } else {
            // If guest, log that information (still using default log)
            Log::info("Guest User Activity: URL: {$url}, Method: {$method}, Timestamp: " . now());

            // Add guest-specific message
            $message .= "Guest User, ";
        }

        // Add the rest of the log message
        $message .= "Action: {$method}, ";
        $message .= "URL: {$url}, ";
        $message .= "Timestamp: " . now();

        // Optionally log the general message as well (can still use default log)
        Log::info($message);

        return $next($request);
    }


}
