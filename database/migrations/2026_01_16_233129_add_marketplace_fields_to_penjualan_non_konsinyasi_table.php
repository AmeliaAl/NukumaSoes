<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->string('jenis_penjualan')->after('pelanggan_id');
            $table->string('no_pesanan')->nullable()->after('jenis_penjualan');
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->dropColumn(['jenis_penjualan', 'no_pesanan']);
        });
    }
};
