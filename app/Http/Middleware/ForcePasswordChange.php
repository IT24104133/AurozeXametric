<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not logged in, let auth handle it
        if (!$user) {
            return $next($request);
        }

        // Allow the change-password page always
        if ($request->is('change-password')) {
            return $next($request);
        }

        // If password not changed yet => force redirect
        if (is_null($user->password_changed_at)) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
