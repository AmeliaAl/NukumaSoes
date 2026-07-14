<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel jurnal sebagai header transaksi jurnal.
     * Struktur sesuai kebutuhan integrasi:
     *   id, tanggal, no_referensi, deskripsi, created_at, updated_at
     */
    public function up(): void
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_referensi')->index(); // PB-001, OH-001, SA-001
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};
