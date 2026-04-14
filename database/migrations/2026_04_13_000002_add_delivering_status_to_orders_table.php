<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite ei enforcea enum-e — 'delivering' staatus töötab juba ilma muutusteta
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // MySQL: lisa 'delivering' enum väärtus
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','preparing','ready','delivering','completed','cancelled','rejected') DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','preparing','ready','completed','cancelled','rejected') DEFAULT 'pending'");
    }
};
