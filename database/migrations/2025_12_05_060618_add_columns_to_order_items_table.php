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
        Schema::table('order_items', function (Blueprint $table) {
            // Check if columns exist before adding
            if (! Schema::hasColumn('order_items', 'order_id')) {
                $table->foreignId('order_id')->after('id')->constrained('orders')->onDelete('cascade');
            }
            if (! Schema::hasColumn('order_items', 'menu_name')) {
                $table->string('menu_name')->after('order_id');
            }
            if (! Schema::hasColumn('order_items', 'price')) {
                $table->decimal('price', 10, 2)->after('menu_name');
            }
            if (! Schema::hasColumn('order_items', 'quantity')) {
                $table->integer('quantity')->after('price');
            }
            if (! Schema::hasColumn('order_items', 'image')) {
                $table->string('image')->nullable()->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('order_items', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('order_items', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('order_items', 'menu_name')) {
                $table->dropColumn('menu_name');
            }
            if (Schema::hasColumn('order_items', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
        });
    }
};
