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
        Schema::create('laba_rugi_manuals', function (Blueprint $table) {
            $table->id();
            $table->string('periode')->unique(); // format Y-m
            $table->decimal('penjualan_bersih', 20, 2)->default(0);
            $table->decimal('persediaan_produk_jadi_awal', 20, 2)->default(0);
            $table->decimal('persediaan_bdp_awal', 20, 2)->default(0);
            $table->decimal('biaya_bahan_baku', 20, 2)->default(0);
            $table->decimal('biaya_tenaga_kerja_langsung', 20, 2)->default(0);
            $table->decimal('biaya_overhead_pabrik', 20, 2)->default(0);
            $table->decimal('persediaan_bdp_akhir', 20, 2)->default(0);
            $table->decimal('persediaan_produk_jadi_akhir', 20, 2)->default(0);
            $table->decimal('biaya_pemasaran', 20, 2)->default(0);
            $table->decimal('biaya_adm_umum', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laba_rugi_manuals');
    }
};
