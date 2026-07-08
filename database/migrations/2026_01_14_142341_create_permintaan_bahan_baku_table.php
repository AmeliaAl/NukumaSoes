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
        Schema::create('permintaan_bahan_baku', function (Blueprint $table) {
            $table->id('id_permintaan_bahan');
            $table->string('nomor_permintaan', 50)->unique();
            $table->foreignId('id_bahan')->constrained('bahan_baku', 'id_bahan')->onDelete('cascade');
            $table->foreignId('id_admin')->constrained('admins', 'id_admin')->onDelete('cascade');
            $table->date('tanggal_permintaan');
            $table->decimal('jumlah_permintaan', 10, 2);
            $table->decimal('harga_per_satuan', 15, 2);
            $table->string('keperluan', 100)->nullable(); // Untuk job nomor berapa
            $table->enum('status_permintaan', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->date('tanggal_disetujui')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('admins', 'id_admin')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_bahan_baku');
    }
};