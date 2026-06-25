<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE detail_penjualan_non_konsinyasi
            MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE detail_penjualan_non_konsinyasi
            MODIFY COLUMN id BIGINT UNSIGNED NOT NULL
        ");
    }
};
