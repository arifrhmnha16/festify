<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($role === 'admin' && Auth::guard('admin')->check()) {
            return $next($request);
        }

        if ($role === 'user' && Auth::guard('web')->check()) {
            return $next($request);
        }

        if (in_array($role, ['loket', 'gate'], true) && Auth::guard('officer')->check() && Auth::guard('officer')->user()->role === $role) {
            return $next($request);
        }

        return app(\App\Http\Controllers\AuthController::class)
            ->redirectToRoleLogin($role)
            ->with('error', 'Silakan login sesuai peran.');
    }
}
