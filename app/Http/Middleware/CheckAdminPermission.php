<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user || !$user->is_admin) {
            abort(403, 'Unauthorized');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->hasAdminPermission($permission)) {
            if ($request->header('X-Inertia')) {
                return Inertia::render('Admin/AccessDenied', [
                    'message' => 'You do not have permission to access this section.',
                ])->toResponse($request)->setStatusCode(403);
            }

            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
