<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_produksi', function (Blueprint $table) {
            // Kode order dari sistem eksternal (kelompok lain)
            $table->string('kode_order_eksternal', 100)->nullable()->after('nomor_job');
            // Nama customer / sumber order dari luar
            $table->string('nama_pemesan', 150)->nullable()->after('kode_order_eksternal');
            // Tanggal penerimaan order dari bagian penjualan
            $table->date('tanggal_terima_order')->nullable()->after('nama_pemesan');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_produksi', function (Blueprint $table) {
            $table->dropColumn(['kode_order_eksternal', 'nama_pemesan', 'tanggal_terima_order']);
        });
    }
};
