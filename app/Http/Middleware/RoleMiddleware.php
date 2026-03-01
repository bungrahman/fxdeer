<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        if (Auth::user()->role !== $role) {
            if ($role === 'ADMIN') {
                return redirect()->route('client.dashboard')->with('error', 'You do not have access to the admin area.');
            }
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
