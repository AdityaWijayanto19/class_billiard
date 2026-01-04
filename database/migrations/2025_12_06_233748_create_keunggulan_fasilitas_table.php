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
        Schema::create('keunggulan_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Keunggulan & Fasilitas Kami');
            $table->text('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keunggulan_fasilitas');
    }
};
