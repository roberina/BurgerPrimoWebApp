<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('admin_role')->nullable()->after('is_admin');
            $table->json('admin_permissions')->nullable()->after('admin_role');
            $table->boolean('is_active')->default(true)->after('admin_permissions');
        });

        // Promote all existing admins to superadmin
        DB::table('users')->where('is_admin', true)->update(['admin_role' => 'superadmin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admin_role', 'admin_permissions', 'is_active']);
        });
    }
};
