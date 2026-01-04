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
        if (Schema::hasTable('tentang_kamis')) {
            Schema::table('tentang_kamis', function (Blueprint $table) {
                if (! Schema::hasColumn('tentang_kamis', 'video_description')) {
                    $table->text('video_description')->nullable()->after('video_url');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tentang_kamis')) {
            Schema::table('tentang_kamis', function (Blueprint $table) {
                if (Schema::hasColumn('tentang_kamis', 'video_description')) {
                    $table->dropColumn('video_description');
                }
            });
        }
    }
};
