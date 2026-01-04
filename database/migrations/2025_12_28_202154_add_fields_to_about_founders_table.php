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
        Schema::table('about_founders', function (Blueprint $table) {
            $table->text('quote')->nullable()->after('description');
            $table->string('signature')->nullable()->after('quote');
            $table->string('position')->nullable()->after('name');
            $table->string('image')->nullable()->after('photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_founders', function (Blueprint $table) {
            $table->dropColumn(['quote', 'signature', 'position', 'image']);
        });
    }
};
