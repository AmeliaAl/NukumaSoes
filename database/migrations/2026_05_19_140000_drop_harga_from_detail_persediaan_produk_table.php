<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_persediaan_produk', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }

    public function down(): void
    {
        Schema::table('detail_persediaan_produk', function (Blueprint $table) {
            $table->unsignedInteger('harga')->default(0)->after('stok_saat_ini');
        });
    }
};
