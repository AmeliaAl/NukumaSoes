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
        Schema::table('faktur_pembelian_item', function (Blueprint $table) {
            $table->string('keterangan', 100)->nullable()->after('qty')->comment('Satuan barang (unit, pcs, kg, dll)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faktur_pembelian_item', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
