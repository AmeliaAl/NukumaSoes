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
        Schema::create('permintaan_bahan_baku_detail', function (Blueprint $table) {
            $table->id('id_permintaan_detail');
            $table->foreignId('id_permintaan_bahan')->constrained('permintaan_bahan_baku', 'id_permintaan_bahan')->onDelete('cascade');
            $table->foreignId('id_bahan')->constrained('bahan_baku', 'id_bahan')->onDelete('restrict');
            $table->decimal('jumlah_permintaan', 15, 2);
            $table->decimal('jumlah_diterima', 15, 2)->default(0);
            $table->enum('status_penerimaan', ['belum', 'partial', 'completed'])->default('belum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_bahan_baku_detail');
    }
};
