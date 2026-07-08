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
        Schema::create('stok_bahan_baku', function (Blueprint $table) {
            $table->id('id_stok');
            $table->foreignId('id_bahan')->constrained('bahan_baku', 'id_bahan')->onDelete('cascade');
            $table->date('tanggal_masuk');
            $table->decimal('jumlah_masuk', 10, 2);
            $table->decimal('harga_per_satuan', 15, 2);
            $table->decimal('sisa_stok', 10, 2); // Untuk FIFO tracking
            $table->string('sumber', 50)->default('permintaan'); // permintaan, lainnya
            $table->text('keterangan')->nullable();
            $table->enum('status', ['tersedia', 'habis'])->default('tersedia');
            $table->timestamps();
            
            // Index untuk query FIFO (cari stok paling lama)
            $table->index(['id_bahan', 'tanggal_masuk', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_bahan_baku');
    }
};