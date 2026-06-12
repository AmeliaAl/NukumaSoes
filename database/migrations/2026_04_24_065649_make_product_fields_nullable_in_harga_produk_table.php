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
        Schema::table('harga_produk', function (Blueprint $table) {
            $table->string('kode_produk')->nullable()->change();
            $table->string('nama_produk')->nullable()->change();
            // Drop unique constraint on kode_produk if it exists, 
            // because multiple categories prices might have null or same codes now
            $table->dropUnique(['kode_produk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_produk', function (Blueprint $table) {
            $table->string('kode_produk')->nullable(false)->unique()->change();
            $table->string('nama_produk')->nullable(false)->change();
        });
    }
};
