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
        Schema::table('orders', function (Blueprint $table) {
            // Check if columns exist before adding
            if (! Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->after('id');
            }
            if (! Schema::hasColumn('orders', 'table_number')) {
                $table->string('table_number')->after('customer_name');
            }
            if (! Schema::hasColumn('orders', 'room')) {
                $table->string('room')->after('table_number');
            }
            if (! Schema::hasColumn('orders', 'total_price')) {
                $table->decimal('total_price', 10, 2)->after('room');
            }
            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('total_price');
            }
            if (! Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('pending')->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('orders', 'total_price')) {
                $table->dropColumn('total_price');
            }
            if (Schema::hasColumn('orders', 'room')) {
                $table->dropColumn('room');
            }
            if (Schema::hasColumn('orders', 'table_number')) {
                $table->dropColumn('table_number');
            }
            if (Schema::hasColumn('orders', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
        });
    }
};
