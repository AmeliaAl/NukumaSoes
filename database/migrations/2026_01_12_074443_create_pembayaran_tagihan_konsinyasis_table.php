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
        Schema::create('pembayaran_konsinyasi', function (Blueprint $table) {
        $table->id();

        $table->foreignId('tagihan_konsinyasi_id')
            ->constrained('tagihan_konsinyasi')
            ->cascadeOnDelete();

        $table->date('tanggal_bayar');
        $table->decimal('jumlah_bayar', 15, 2);
        $table->enum('metode_bayar', ['transfer']);
        $table->string('bukti_bayar')->nullable();

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_tagihan_konsinyasis');
    }
};
