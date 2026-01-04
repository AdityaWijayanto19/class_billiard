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
        Schema::create('notification_sounds', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama audio (contoh: "Bell", "Chime", dll)
            $table->string('filename'); // Nama file audio (contoh: "bell.mp3")
            $table->string('file_path'); // Path ke file audio
            $table->boolean('is_default')->default(false); // Apakah ini audio default
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_sounds');
    }
};
