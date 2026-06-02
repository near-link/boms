<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request — checks if user has the required role.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            if ($request->user()) {
                return $request->user()->isVendor()
                    ? redirect()->route('vendor.dashboard')
                    : redirect()->route('shop.index');
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
