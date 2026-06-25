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
        Schema::create('perolehan_aset', function (Blueprint $table) {
            $table->id();

                // RELASI KE FAKTUR
            $table->foreignId('id_faktur')
                ->nullable()
                ->constrained('faktur_pembelian')
                ->nullOnDelete();

            // RELASI KE ITEM FAKTUR
            $table->foreignId('id_faktur_item')
                ->nullable()
                ->constrained('faktur_pembelian_item')
                ->nullOnDelete();

            $table->foreignId('id_transaksi')
                ->constrained('transaksi_aset')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('id_vendor')
                ->constrained('vendor')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('tanggal_faktur');

            $table->string('nama_aset');
            $table->foreignId('id_kategori')
                ->constrained('kategori_aset')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('kode_lokasi')
                ->constrained('lokasi_aset')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('tanggal_pakai');
            $table->integer('qty');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('biaya_lain', 15, 2)->default(0);
            $table->decimal('total_perolehan', 15, 2);
            $table->integer('masa_manfaat'); // tahun
            $table->string('metode_penyusutan');
            $table->decimal('nilai_residu', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perolehan_aset');
    }
};
