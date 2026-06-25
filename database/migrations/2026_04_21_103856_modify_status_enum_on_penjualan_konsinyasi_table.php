<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE penjualan_konsinyasi
            MODIFY status ENUM('BELUM TERJUAL', 'SEBAGIAN TERJUAL', 'SELESAI')
            NOT NULL DEFAULT 'BELUM TERJUAL'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE penjualan_konsinyasi
            MODIFY status ENUM('BELUM TERJUAL', 'PIUTANG', 'LUNAS')
            NOT NULL DEFAULT 'BELUM TERJUAL'
        ");
    }
};