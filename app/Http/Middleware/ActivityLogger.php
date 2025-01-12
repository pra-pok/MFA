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

        if ($user) {

            $teamId = $user->team_id;
            $userId = $user->id;
            $logger = teamUserLogger($teamId, $userId);
            $logger->info('User activity recorded.', [
                'action' => $method,
                'url' => $url,
                'timestamp' => now(),
            ]);
        } else {

            Log::info("Guest User Activity: URL: {$url}, Method: {$method}, Timestamp: " . now());
        }

        return $next($request);
    }


}
