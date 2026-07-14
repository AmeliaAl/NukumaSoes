<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah unique constraint pada kode_akun di tabel coas
     * untuk menjaga integritas data di level database.
     */
    public function up(): void
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->unique('kode_akun', 'coas_kode_akun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->dropUnique('coas_kode_akun_unique');
        });
    }
};
