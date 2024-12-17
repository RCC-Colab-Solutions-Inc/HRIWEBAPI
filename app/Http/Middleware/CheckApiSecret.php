<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiSecret
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the API_SECRET from the environment
        $apiSecret = env('APIKEY');
        
        // Check if the API_SECRET exists in the request header
        $apiSecretInRequest = $request->header('API_SECRET');
        
        // Compare the secret passed in the request to the value in .env
        if ($apiSecretInRequest !== $apiSecret) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // If the API_SECRET matches, allow the request to proceed
        return $next($request);
    }
}
