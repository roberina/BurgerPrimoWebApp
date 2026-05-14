<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('SUPER_ADMIN_EMAIL', 'superadmin@burgerprimo.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'superpassword');
        $name     = env('SUPER_ADMIN_NAME', 'Super Admin');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'               => $name,
                'password'           => Hash::make($password),
                'is_admin'           => true,
                'admin_role'         => 'superadmin',
                'admin_permissions'  => null,
                'is_active'          => true,
                'email_verified_at'  => now(),
            ]
        );

        $this->command->info("Super admin ready: {$email}");
        $this->command->warn('Set SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD in .env before running in production.');
    }
}
