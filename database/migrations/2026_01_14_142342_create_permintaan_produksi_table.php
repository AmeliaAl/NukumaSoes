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
        Schema::create('permintaan_produksi', function (Blueprint $table) {
            $table->id('id_permintaan_produksi');
            $table->string('nomor_job', 50)->unique();
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->onDelete('cascade');
            $table->foreignId('id_admin')->constrained('admins', 'id_admin')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('jumlah_produksi', 10, 2);
            $table->enum('status', ['pending', 'proses', 'selesai'])->default('pending');
            
            // Job Order Costing Components
            $table->decimal('total_biaya_bahan', 15, 2)->default(0);
            $table->decimal('total_biaya_tenaga_kerja', 15, 2)->default(0);
            $table->decimal('total_biaya_overhead', 15, 2)->default(0);
            $table->decimal('total_biaya_produksi', 15, 2)->default(0);
            $table->decimal('harga_pokok_per_unit', 15, 2)->default(0);
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_produksi');
    }
};