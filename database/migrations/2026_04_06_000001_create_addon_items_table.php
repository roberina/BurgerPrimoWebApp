<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addon_items', function (Blueprint $table) {
            $table->id();
            // type: 'size' | 'drink' | 'sauce' | 'fries'
            $table->string('type', 30);
            $table->string('name');
            // Extra price on top of the base item price
            $table->decimal('price', 8, 2)->default(0);
            // For size: small/medium/large identifier; for others: optional slug
            $table->string('slug', 60)->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_items');
    }
};
