<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTrackerOrigin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('origin') ?? $request->query('origin') ?? null;
        $allowed = config('tracker.allowed_origins', []);

        $response = $next($request);

        if ($origin && in_array($origin, $allowed)) {
            // CORS response headers for allowed origins
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Vary', 'Origin');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type,Accept,Origin');
        }

        return $response;
    }
}
