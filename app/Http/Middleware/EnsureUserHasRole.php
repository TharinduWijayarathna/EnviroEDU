<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('home');
        }

        foreach ($roles as $role) {
            $enum = Role::tryFrom($role);
            if ($enum && $request->user()->hasRole($enum)) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard');
    }
}
