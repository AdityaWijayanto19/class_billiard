<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('food_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->integer('quantity')->default(0)->comment('Stok tersedia');
            $table->integer('reorder_level')->default(10)->comment('Level minimum sebelum perlu reorder');
            $table->timestamp('last_restocked_at')->nullable();
            $table->timestamps();

            $table->unique('menu_id');
            $table->index('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_inventories');
    }
};
