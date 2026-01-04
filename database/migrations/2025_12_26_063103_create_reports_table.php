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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date'); // Tanggal rekapan
            $table->date('start_date'); // Tanggal mulai periode
            $table->date('end_date'); // Tanggal akhir periode
            $table->integer('total_orders'); // Total order yang di-rekap
            $table->decimal('total_revenue', 15, 2); // Total pendapatan
            $table->decimal('cash_revenue', 15, 2)->default(0); // Pendapatan cash
            $table->decimal('qris_revenue', 15, 2)->default(0); // Pendapatan QRIS
            $table->decimal('transfer_revenue', 15, 2)->default(0); // Pendapatan transfer
            $table->text('order_summary')->nullable(); // Ringkasan order (JSON)
            $table->string('created_by')->nullable(); // User yang membuat rekapan
            $table->timestamps();

            // Index untuk pencarian berdasarkan tanggal
            $table->index('report_date');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
