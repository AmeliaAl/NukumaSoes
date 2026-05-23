<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('no_retur')->unique();
            $table->date('tanggal');

            // Sumber retur: salah satu diisi, yang lain null
            $table->foreignId('penjualan_non_konsinyasi_id')
                ->nullable()
                ->constrained('penjualan_non_konsinyasi')
                ->nullOnDelete();

            $table->foreignId('penjualan_konsinyasi_id')
                ->nullable()
                ->constrained('penjualan_konsinyasi')
                ->nullOnDelete();

            $table->text('alasan')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });

        Schema::create('detail_retur_penjualan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('retur_id')
                ->constrained('retur_penjualan')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->restrictOnDelete();

            $table->integer('qty');
            $table->text('kondisi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_retur_penjualan');
        Schema::dropIfExists('retur_penjualan');
    }
};
