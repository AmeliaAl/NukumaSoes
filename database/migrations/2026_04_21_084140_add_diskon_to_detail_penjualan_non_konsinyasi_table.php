<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->bigInteger('diskon')->default(0)->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('detail_penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->dropColumn('diskon');
        });
    }
};