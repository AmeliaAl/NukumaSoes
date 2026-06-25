<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penjualan_konsinyasi', function (Blueprint $table) {
            $table->date('tanggal_kirim')->nullable()->after('jatuh_tempo');

            // diskon total transaksi (nominal rupiah)
            $table->unsignedBigInteger('diskon')->default(0)->after('total_konsinyasi');
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_konsinyasi', function (Blueprint $table) {
            $table->dropColumn(['tanggal_kirim', 'diskon']);
        });
    }
};