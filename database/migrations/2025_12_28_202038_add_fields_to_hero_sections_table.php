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
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->string('tagline')->nullable()->after('subtitle');
            $table->string('cta_text_1')->nullable()->after('tagline');
            $table->string('cta_text_2')->nullable()->after('cta_text_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->dropColumn(['tagline', 'cta_text_1', 'cta_text_2']);
        });
    }
};
