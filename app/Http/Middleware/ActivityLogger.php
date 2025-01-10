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

        $message = "User Activity: ";
        $message .= $user ? "User ID: {$user->name}, " : "Guest User, ";
        $message .= "Action: {$method}, ";
        $message .= "URL: {$url}, ";
        $message .= "Timestamp: " . now();

        // Log the message
        Log::info($message);
        return $next($request);
    }
}
