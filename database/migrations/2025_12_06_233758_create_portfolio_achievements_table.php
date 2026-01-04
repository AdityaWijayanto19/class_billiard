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
        Schema::create('portfolio_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Portfolio & Achievement');
            $table->text('subtitle')->nullable();
            $table->string('type')->default('achievement'); // achievement or gallery
            $table->string('icon')->nullable();
            $table->string('number')->nullable();
            $table->string('label')->nullable();
            $table->string('image')->nullable(); // for gallery
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
        Schema::dropIfExists('portfolio_achievements');
    }
};
