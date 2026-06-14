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
        Schema::create('laporan_konsinyasi', function (Blueprint $table) {
        $table->id();
        $table->string('no_laporan')->unique();

        $table->foreignId('penjualan_konsinyasi_id')
            ->constrained('penjualan_konsinyasi')
            ->cascadeOnDelete();

        $table->string('no_po_mitra');
        $table->date('periode_awal');
        $table->date('periode_akhir');

        $table->decimal('total_laporan', 15, 2);

        $table->text('keterangan')->nullable();
        $table->timestamps();
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_konsinyasi');
    }
};
