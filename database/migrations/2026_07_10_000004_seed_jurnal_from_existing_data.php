<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Migration ini dikosongkan secara sengaja.
     *
     * Data transaksi lama (Setoran Modal, Pembelian, Overhead) dipindahkan
     * ke tabel jurnal dan jurnal_detail menggunakan Artisan Command yang
     * dijalankan MANUAL, bukan melalui migration.
     *
     * Cara menjalankan migrasi data lama:
     *   php artisan jurnal:migrate-data-lama
     *
     * Command tersebut sudah dilengkapi pengecekan duplikasi sehingga
     * aman dijalankan berulang kali.
     *
     * Migration ini tetap ada agar urutan migration tidak berubah
     * dan tidak menimbulkan error saat php artisan migrate:fresh.
     */
    public function up(): void
    {
        // Kosong — lihat App\Console\Commands\MigrasiJurnalDataLama
    }

    public function down(): void
    {
        // Kosong
    }
};
