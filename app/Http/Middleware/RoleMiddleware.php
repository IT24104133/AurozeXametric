<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // allow multiple roles: role:admin,teacher
        $allowed = array_map('trim', explode(',', $role));

        if (!in_array($user->role, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
