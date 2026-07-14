<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom status_permintaan ke tabel pembelians.
     *
     * Nilai: 'lengkap' | 'sebagian' | null
     * Default null = belum ditentukan / tidak terkait permintaan produksi.
     *
     * Setelah integrasi modul Produksi:
     * - 'lengkap'  → semua bahan baku yang diminta sudah terpenuhi
     * - 'sebagian' → baru sebagian yang dipenuhi, masih perlu pembelian lagi
     * - null       → pembelian tidak terkait permintaan produksi
     */
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->string('status_permintaan')->nullable()->after('pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('status_permintaan');
        });
    }
};
