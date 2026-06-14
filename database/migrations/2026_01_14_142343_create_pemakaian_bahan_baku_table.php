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
        Schema::create('pemakaian_bahan_baku', function (Blueprint $table) {
            $table->id('id_pemakaian');
            $table->foreignId('id_permintaan_produksi')->constrained('permintaan_produksi', 'id_permintaan_produksi')->onDelete('cascade');
            $table->foreignId('id_bahan')->constrained('bahan_baku', 'id_bahan')->onDelete('cascade');
            $table->foreignId('id_stok')->constrained('stok_bahan_baku', 'id_stok')->onDelete('cascade'); // FIFO tracking
            $table->foreignId('id_admin')->constrained('admins', 'id_admin')->onDelete('cascade');
            $table->date('tanggal_pemakaian');
            $table->decimal('jumlah_pakai', 10, 2);
            $table->decimal('harga_satuan', 15, 2); // Dari stok FIFO
            $table->decimal('total_biaya', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk query per job
            $table->index('id_permintaan_produksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_bahan_baku');
    }
};