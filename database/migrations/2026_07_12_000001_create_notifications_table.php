<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel notifications — placeholder untuk integrasi modul Produksi.
     *
     * Jenis notifikasi yang akan digunakan setelah integrasi:
     * - 'produksi_to_pembelian': Produksi membuat Permintaan Bahan Baku → beri tahu Pembelian
     * - 'pembelian_to_produksi': Pembelian selesai → beri tahu Produksi (status: Lengkap/Sebagian)
     *
     * Untuk saat ini tabel kosong karena modul Produksi belum terintegrasi.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');           // produksi_to_pembelian | pembelian_to_produksi
            $table->string('title');          // Judul notifikasi
            $table->text('message');          // Isi pesan
            $table->string('reference_no')->nullable(); // PR-001, PB-001
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
