<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::select('id', 'name', 'email', 'is_admin', 'admin_role', 'admin_permissions', 'is_active', 'is_courier', 'created_at')
            ->orderBy('name')
            ->get();

        $data = ['users' => $users];

        if (auth()->user()->isSuperAdmin()) {
            $data['validPermissions'] = ['users', 'menu', 'burger', 'announcements', 'orders'];
        }

        return Inertia::render('Admin/Users/Index', $data);
    }

    public function destroy(User $user)
    {
        if ($user->is_admin) {
            abort(403, 'Admin accounts cannot be deleted here.');
        }

        $user->delete();

        return redirect()->back()->with('success', "{$user->name} kustutatud.");
    }

    public function toggleCourier(User $user)
    {
        $user->update(['is_courier' => !$user->is_courier]);

        return redirect()->back()->with('success', $user->is_courier
            ? "{$user->name} on nüüd kuller."
            : "{$user->name} pole enam kuller.");
    }
}
