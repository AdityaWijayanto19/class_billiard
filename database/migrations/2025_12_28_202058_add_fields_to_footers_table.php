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
        Schema::table('footers', function (Blueprint $table) {
            $table->string('opening_hours')->nullable()->after('saturday_sunday_hours');
            $table->text('map_url')->nullable()->after('google_maps_url');
            $table->string('whatsapp')->nullable()->after('youtube_url');
            $table->string('copyright')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('footers', function (Blueprint $table) {
            $table->dropColumn(['opening_hours', 'map_url', 'whatsapp', 'copyright']);
        });
    }
};
