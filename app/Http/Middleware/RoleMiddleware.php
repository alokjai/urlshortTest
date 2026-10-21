<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(403, 'You do not have permission to access this resource.');
        }

        $roles = explode(',', $role);
        $hasRole = false;

        foreach ($roles as $r) {
            if (auth()->user()->hasRole(trim($r))) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
