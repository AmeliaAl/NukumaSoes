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
        Schema::create('persediaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_faktur')
                ->constrained('faktur_pembelian')
                ->cascadeOnDelete();

            $table->foreignId('id_faktur_item')
                ->constrained('faktur_pembelian_item')
                ->cascadeOnDelete();

            $table->foreignId('id_kategori')
                ->constrained('kategori_aset')
                ->restrictOnDelete();

            $table->foreignId('id_vendor')
                ->constrained('vendor')
                ->restrictOnDelete();

            $table->string('nama_barang');
            $table->integer('qty');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total', 15, 2);

            $table->date('tanggal_masuk');
            $table->foreignId('kode_lokasi')
                ->nullable()
                ->constrained('lokasi_aset')
                ->nullOnDelete();
            $table->timestamps();
            $table->unique('id_faktur_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persediaan');
    }
};
