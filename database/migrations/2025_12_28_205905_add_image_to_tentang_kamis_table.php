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
        Schema::table('tentang_kamis', function (Blueprint $table) {
            if (! Schema::hasColumn('tentang_kamis', 'image')) {
                $table->string('image')->nullable()->after('subtitle');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tentang_kamis', function (Blueprint $table) {
            if (Schema::hasColumn('tentang_kamis', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
