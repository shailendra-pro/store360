<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiBusinessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid token.',
            ], 401);
        }

        $user = Auth::guard('sanctum')->user();

        // Check if user is a business account
        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.',
            ], 403);
        }

        // Check if business account is active
        if (!$user->isBusinessActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your business account is not active. Please contact administrator.',
            ], 403);
        }

        return $next($request);
    }
}
