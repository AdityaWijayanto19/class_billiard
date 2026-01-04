<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('hero_sections', 'cta_link_1')) {
                $table->string('cta_link_1')->nullable()->after('cta_text_1');
            }
        });
    }

    public function down()
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            if (Schema::hasColumn('hero_sections', 'cta_link_1')) {
                $table->dropColumn('cta_link_1');
            }
        });
    }
};
