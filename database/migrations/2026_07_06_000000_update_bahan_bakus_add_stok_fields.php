<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah field stok_minimum dan stok_saat_ini ke tabel bahan_bakus.
     * Field ini dikelola oleh modul Produksi, untuk saat ini default 0.
     */
    public function up(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->decimal('stok_minimum', 12, 2)->default(0)->after('isi_per_kemasan');
            $table->decimal('stok_saat_ini', 12, 2)->default(0)->after('stok_minimum');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropColumn(['stok_minimum', 'stok_saat_ini']);
        });
    }
};
