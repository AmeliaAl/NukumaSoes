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
        Schema::table('laba_rugi_manuals', function (Blueprint $table) {
            $table->decimal('harga_pokok_produksi', 20, 2)->default(0)->after('persediaan_bdp_akhir');
            $table->decimal('harga_pokok_penjualan', 20, 2)->default(0)->after('persediaan_produk_jadi_akhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laba_rugi_manuals', function (Blueprint $table) {
            //
        });
    }
};
