<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'navbar_label')) {
                $table->string('navbar_label')->nullable()->after('whatsapp');
            }
            if (! Schema::hasColumn('contacts', 'navbar_link')) {
                $table->string('navbar_link')->nullable()->after('navbar_label');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'navbar_link')) {
                $table->dropColumn('navbar_link');
            }
            if (Schema::hasColumn('contacts', 'navbar_label')) {
                $table->dropColumn('navbar_label');
            }
        });
    }
};
