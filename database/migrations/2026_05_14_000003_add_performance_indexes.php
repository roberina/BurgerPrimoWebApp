<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('delivery_method');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->index('is_available');
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->index('category');
            $table->index('is_available');
        });

        Schema::table('custom_burgers', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['delivery_method']);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['is_available']);
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_available']);
        });

        Schema::table('custom_burgers', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
