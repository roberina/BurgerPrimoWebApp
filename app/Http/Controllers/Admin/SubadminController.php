<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubadminController extends Controller
{
    private const VALID_PERMISSIONS = ['orders', 'menu', 'burger', 'users', 'announcements'];

    public function index(): Response
    {
        $subadmins = User::where('admin_role', 'subadmin')
            ->select('id', 'name', 'email', 'admin_permissions', 'is_active', 'created_at')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Subadmins/Index', [
            'subadmins'       => $subadmins,
            'validPermissions' => self::VALID_PERMISSIONS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:8'],
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['string', Rule::in(self::VALID_PERMISSIONS)],
        ]);

        User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'is_admin'          => true,
            'admin_role'        => 'subadmin',
            'admin_permissions' => $validated['permissions'],
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Subadmin created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $this->assertSubadmin($user);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password'      => ['nullable', 'string', 'min:8'],
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['string', Rule::in(self::VALID_PERMISSIONS)],
        ]);

        $data = [
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'admin_permissions' => $validated['permissions'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Subadmin updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $this->assertSubadmin($user);

        $user->update(['is_active' => !$user->is_active]);

        return redirect()->back()->with('success', $user->is_active
            ? 'Subadmin activated.'
            : 'Subadmin deactivated.');
    }

    public function destroy(User $user)
    {
        $this->assertSubadmin($user);

        $user->delete();

        return redirect()->back()->with('success', 'Subadmin deleted.');
    }

    public function demote(User $user)
    {
        $this->assertSubadmin($user);

        $user->update([
            'is_admin'          => false,
            'admin_role'        => null,
            'admin_permissions' => null,
        ]);

        return redirect()->back()->with('success', "{$user->name} admin õigused eemaldatud.");
    }

    public function promote(Request $request, User $user)
    {
        if ($user->is_admin) {
            abort(422, 'User is already an admin.');
        }

        $validated = $request->validate([
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['string', Rule::in(self::VALID_PERMISSIONS)],
        ]);

        $user->update([
            'is_admin'          => true,
            'admin_role'        => 'subadmin',
            'admin_permissions' => $validated['permissions'],
            'is_active'         => true,
        ]);

        return redirect()->back()->with('success', "{$user->name} on nüüd subadmin.");
    }

    private function assertSubadmin(User $user): void
    {
        if ($user->admin_role === 'superadmin') {
            abort(403, 'The super admin account cannot be modified through this interface.');
        }

        if ($user->admin_role !== 'subadmin') {
            abort(404);
        }
    }
}
