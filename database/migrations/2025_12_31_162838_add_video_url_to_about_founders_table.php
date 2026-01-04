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
            $table->text('video_url')->nullable()->after('image')->comment('YouTube video URL for founder section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_founders', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
};
