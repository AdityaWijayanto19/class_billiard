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
                if (! Schema::hasColumn('tentang_kamis', 'title')) {
                    $table->string('title')->default('Tentang Kami');
                }
                if (! Schema::hasColumn('tentang_kamis', 'subtitle')) {
                    $table->text('subtitle')->nullable();
                }
                if (! Schema::hasColumn('tentang_kamis', 'visi')) {
                    $table->text('visi')->nullable();
                }
                if (! Schema::hasColumn('tentang_kamis', 'misi')) {
                    $table->text('misi')->nullable();
                }
                if (! Schema::hasColumn('tentang_kamis', 'arah_gerak')) {
                    $table->text('arah_gerak')->nullable();
                }
                if (! Schema::hasColumn('tentang_kamis', 'video_url')) {
                    $table->string('video_url')->nullable();
                }
                if (! Schema::hasColumn('tentang_kamis', 'is_active')) {
                    $table->boolean('is_active')->default(true);
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
                if (Schema::hasColumn('tentang_kamis', 'is_active')) {
                    $table->dropColumn('is_active');
                }
                if (Schema::hasColumn('tentang_kamis', 'video_url')) {
                    $table->dropColumn('video_url');
                }
                if (Schema::hasColumn('tentang_kamis', 'arah_gerak')) {
                    $table->dropColumn('arah_gerak');
                }
                if (Schema::hasColumn('tentang_kamis', 'misi')) {
                    $table->dropColumn('misi');
                }
                if (Schema::hasColumn('tentang_kamis', 'visi')) {
                    $table->dropColumn('visi');
                }
                if (Schema::hasColumn('tentang_kamis', 'subtitle')) {
                    $table->dropColumn('subtitle');
                }
                if (Schema::hasColumn('tentang_kamis', 'title')) {
                    $table->dropColumn('title');
                }
            });
        }
    }
};
