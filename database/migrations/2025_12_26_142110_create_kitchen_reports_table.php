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
        Schema::create('kitchen_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // ID order yang sudah completed
            $table->string('customer_name');
            $table->string('table_number');
            $table->string('room');
            $table->decimal('total_price', 15, 2);
            $table->enum('payment_method', ['cash', 'qris', 'transfer']);
            $table->json('order_items'); // Menyimpan detail order items sebagai JSON
            $table->date('order_date'); // Tanggal order untuk filtering harian
            $table->dateTime('completed_at'); // Waktu order diselesaikan
            $table->timestamps();

            // Foreign key ke orders table
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // Index untuk pencarian berdasarkan tanggal
            $table->index('order_date');
            $table->index('completed_at');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_reports');
    }
};
