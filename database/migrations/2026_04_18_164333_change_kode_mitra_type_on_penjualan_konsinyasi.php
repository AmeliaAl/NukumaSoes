<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penjualan_konsinyasi', function (Blueprint $table) {
            $table->dropForeign(['kode_mitra']);
        });

        Schema::table('penjualan_konsinyasi', function (Blueprint $table) {
            $table->string('kode_mitra', 255)->change();
        });

        Schema::table('penjualan_konsinyasi', function (Blueprint $table) {
            $table->foreign('kode_mitra')
                ->references('kode_mitra')
                ->on('mitra')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_konsinyasi', function (Blueprint $table) {
            $table->dropForeign(['kode_mitra']);
        });

        Schema::table('penjualan_konsinyasi', function (Blueprint $table) {
            $table->unsignedBigInteger('kode_mitra')->change();
        });

        Schema::table('penjualan_konsinyasi', function (Blueprint $table) {
            $table->foreign('kode_mitra')
                ->references('id')
                ->on('mitra')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }
};