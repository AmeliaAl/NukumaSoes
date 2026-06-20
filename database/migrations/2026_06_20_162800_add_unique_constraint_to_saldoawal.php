<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('saldoawal', function (Blueprint $table) {
            // Tambahkan unique constraint untuk kombinasi akun_id, bulan, tahun
            $table->unique(['akun_id', 'bulan', 'tahun'], 'unique_akun_bulan_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saldoawal', function (Blueprint $table) {
            $table->dropUnique('unique_akun_bulan_tahun');
        });
    }
};
