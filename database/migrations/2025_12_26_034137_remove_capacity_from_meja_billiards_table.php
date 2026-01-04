<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meja_billiards', function (Blueprint $table) {
            if (Schema::hasColumn('meja_billiards', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meja_billiards', function (Blueprint $table) {
            if (! Schema::hasColumn('meja_billiards', 'capacity')) {
                // Only add capacity if slug column exists, otherwise add it at the end
                if (Schema::hasColumn('meja_billiards', 'slug')) {
                    $table->integer('capacity')->default(4)->after('slug');
                } else {
                    $table->integer('capacity')->default(4);
                }
            }
        });
    }
};
