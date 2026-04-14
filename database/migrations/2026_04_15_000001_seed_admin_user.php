<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('users')->where('email', 'admin@burgerprimo.com')->exists();
        if (!$exists) {
            DB::table('users')->insert([
                'name' => 'Admin User',
                'email' => 'admin@burgerprimo.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'admin@burgerprimo.com')->delete();
    }
};
