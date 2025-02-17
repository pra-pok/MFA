<?php
namespace App\Http\Middleware;
use Closure;
class HandleCors
{
    public function handle($request, Closure $next)
    {
        $allowedOrigins = [
            'https://myfreeadmission.com',
            'http://127.0.0.1:8000',
            'http://192.168.1.86:8000'
        ];

        $origin = $request->headers->get('Origin');
        if (in_array($origin, $allowedOrigins)) {
            return $next($request)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }
        return $next($request);
    }

}
