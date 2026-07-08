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
        Schema::create('penerimaan_bahan_baku_detail', function (Blueprint $table) {
            $table->id('id_penerimaan_detail');
            $table->foreignId('id_penerimaan')->constrained('penerimaan_bahan_baku', 'id_penerimaan')->onDelete('cascade');
            $table->foreignId('id_bahan')->constrained('bahan_baku', 'id_bahan')->onDelete('restrict');
            $table->decimal('jumlah_diterima', 15, 2);
            $table->decimal('harga_per_satuan', 15, 2);
            $table->decimal('total_biaya', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_bahan_baku_detail');
    }
};
