<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_konsinyasi', function (Blueprint $table) {
            $table->id();

            // Relasi ke penjualan konsinyasi
            $table->foreignId('no_konsinyasi')
                ->constrained('penjualan_konsinyasi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Tanggal setoran diterima
            $table->date('tanggal_setor');

            // Jumlah uang yang disetor
            $table->decimal('jumlah_setor', 15, 2);
       
            $table->string('metode_bayar');

            $table->string('bukti_bayar')->nullable();

            // Catatan opsional
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_konsinyasi');
    }
};
