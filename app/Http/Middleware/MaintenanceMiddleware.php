<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class MaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenance = Setting::get('maintenance_mode', '0') === '1';

        if ($isMaintenance) {
            // Allow Admins to bypass maintenance
            if (Auth::check() && Auth::user()->role === 'ADMIN') {
                return $next($request);
            }

            // Also allow the login/logout pages so admins can log in
            if ($request->is('login*') || $request->is('logout*')) {
                return $next($request);
            }

            // Show maintenance page for others
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
