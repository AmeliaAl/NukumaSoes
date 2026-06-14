<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pembayaran_konsinyasi MODIFY COLUMN metode_bayar ENUM('transfer', 'tunai', 'qris') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pembayaran_konsinyasi MODIFY COLUMN metode_bayar ENUM('transfer') NOT NULL");
    }
};
