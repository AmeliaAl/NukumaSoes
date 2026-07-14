<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus seluruh data bahan_bakus dan transaksi pembelian yang berkaitan.
     * Struktural tabel, migration, relasi, dan controller tetap utuh.
     */
    public function up(): void
    {
        // Urutan: hapus detail dulu (FK), lalu header
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pembelian_details')->truncate();
        DB::table('pembelians')->truncate();
        DB::table('bahan_bakus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        // Data tidak bisa di-restore — migration satu arah
    }
};
