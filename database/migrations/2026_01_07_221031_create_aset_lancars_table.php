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
        Schema::create('aset_lancar', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->foreignId('id_kategori')
                ->constrained('kategori_aset')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('stok_tersedia')->default(0);
            //$table->integer('stok_minimum')->default(0);
            $table->decimal('harga_satuan_rata', 18, 2)->default(0);
            $table->decimal('nilai_total', 18, 2)->default(0);
            $table->date('tanggal_update_terakhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->index('nama_barang');
            $table->index('id_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset_lancar');
    }
};
