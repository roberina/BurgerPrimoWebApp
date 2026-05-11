<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: rename data values
        DB::table('orders')->where('status', 'pending')->update(['status' => 'pending_confirmation']);
        DB::table('orders')->where('status', 'delivering')->update(['status' => 'picked_up']);
        DB::table('orders')->where('status', 'completed')->update(['status' => 'delivered']);
        DB::table('orders')->where('status', 'rejected')->update(['status' => 'refunded']);

        // Step 2: alter the enum column — MySQL only (SQLite stores as string, no constraint needed)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending_confirmation',
                'confirmed',
                'preparing',
                'ready',
                'awaiting_courier',
                'picked_up',
                'delivered',
                'cancelled',
                'refunded'
            ) NOT NULL DEFAULT 'pending_confirmation'");
        }
    }

    public function down(): void
    {
        DB::table('orders')->where('status', 'pending_confirmation')->update(['status' => 'pending']);
        DB::table('orders')->where('status', 'picked_up')->update(['status' => 'delivering']);
        DB::table('orders')->where('status', 'delivered')->update(['status' => 'completed']);
        DB::table('orders')->where('status', 'refunded')->update(['status' => 'rejected']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'awaiting_courier',
                'delivering',
                'completed',
                'cancelled',
                'rejected'
            ) NOT NULL DEFAULT 'pending'");
        }
    }
};
