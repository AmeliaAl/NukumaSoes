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
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->string('kode_produk')->nullable()->change();
            $table->string('nama_produk')->nullable()->change();
            $table->decimal('harga', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->string('kode_produk')->nullable(false)->change();
            $table->string('nama_produk')->nullable(false)->change();
            $table->decimal('harga', 15, 2)->nullable(false)->change();
        });
    }
};
