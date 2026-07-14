<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename tabel bahan_bakus → bahan_baku.
     * Foreign key di pembelian_details (bahan_baku_id → bahan_bakus)
     * akan otomatis ikut karena MySQL menyesuaikan FK saat rename.
     */
    public function up(): void
    {
        Schema::rename('bahan_bakus', 'bahan_baku');
    }

    public function down(): void
    {
        Schema::rename('bahan_baku', 'bahan_bakus');
    }
};
