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
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('id_produk', 'kode_produk');
        });
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->renameColumn('id_produk', 'kode_produk');
        });
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->renameColumn('id_produk', 'kode_produk');
        });
        Schema::table('kartu_stok_entries', function (Blueprint $table) {
            $table->renameColumn('id_produk', 'kode_produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('kode_produk', 'id_produk');
        });
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->renameColumn('kode_produk', 'id_produk');
        });
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->renameColumn('kode_produk', 'id_produk');
        });
        Schema::table('kartu_stok_entries', function (Blueprint $table) {
            $table->renameColumn('kode_produk', 'id_produk');
        });
    }
};
