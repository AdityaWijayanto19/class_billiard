<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Makanan, Minuman, dsb
            $table->string('slug')->unique();
            $table->integer('order_priority')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_menus');
    }
};
