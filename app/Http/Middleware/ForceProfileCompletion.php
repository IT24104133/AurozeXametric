<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceProfileCompletion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) return $next($request);

        // allow profile routes always
        if ($request->is('profile') || $request->is('profile/*')) {
            return $next($request);
        }

        // if profile not completed -> redirect
        if (is_null($user->profile_completed_at)) {
            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}
