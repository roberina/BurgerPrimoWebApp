<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || !$user->isSuperAdmin()) {
            if ($request->wantsJson() || $request->header('X-Inertia')) {
                return response()->json(['message' => 'Super admin access required.'], 403);
            }

            abort(403, 'Super admin access required.');
        }

        return $next($request);
    }
}
