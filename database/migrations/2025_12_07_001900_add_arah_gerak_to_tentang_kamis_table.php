<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tentang_kamis')) {
            Schema::table('tentang_kamis', function (Blueprint $table) {
                if (! Schema::hasColumn('tentang_kamis', 'arah_gerak')) {
                    $table->text('arah_gerak')->nullable()->after('misi');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tentang_kamis')) {
            Schema::table('tentang_kamis', function (Blueprint $table) {
                if (Schema::hasColumn('tentang_kamis', 'arah_gerak')) {
                    $table->dropColumn('arah_gerak');
                }
            });
        }
    }
};
