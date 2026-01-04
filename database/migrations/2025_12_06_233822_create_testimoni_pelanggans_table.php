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
        Schema::create('testimoni_pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Testimoni Pelanggan');
            $table->text('subtitle')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_role')->nullable();
            $table->text('testimonial')->nullable();
            $table->integer('rating')->default(5);
            $table->string('photo')->nullable();
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
        Schema::dropIfExists('testimoni_pelanggans');
    }
};
