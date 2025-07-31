<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BusinessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/business/login');
        }

        if (!Auth::user()->isBusiness()) {
            Auth::logout();
            return redirect('/business/login')->withErrors([
                'username' => 'You do not have business privileges.',
            ]);
        }

        if (!Auth::user()->isBusinessActive()) {
            Auth::logout();
            return redirect('/business/login')->withErrors([
                'username' => 'Your business account is not active. Please contact administrator.',
            ]);
        }

        return $next($request);
    }
}
