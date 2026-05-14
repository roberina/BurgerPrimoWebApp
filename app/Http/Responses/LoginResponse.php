<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class LoginResponse implements LoginResponseContract
{
    private const SUBADMIN_REDIRECT_MAP = [
        'orders'        => '/admin/orders',
        'menu'          => '/admin/menu/categories',
        'burger'        => '/admin/ingredients',
        'users'         => '/admin/users',
        'announcements' => '/admin/announcements',
    ];

    public function toResponse($request): JsonResponse|RedirectResponse
    {
        $user = auth()->user();

        if ($user && $user->is_admin) {
            if ($user->admin_role === 'subadmin') {
                foreach ($user->admin_permissions ?? [] as $perm) {
                    if (isset(self::SUBADMIN_REDIRECT_MAP[$perm])) {
                        return redirect(self::SUBADMIN_REDIRECT_MAP[$perm]);
                    }
                }
                // No permissions configured — send to users page as fallback
                return redirect('/admin/users');
            }

            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/');
    }
}
