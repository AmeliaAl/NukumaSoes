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
        Schema::table('kartu_stok_entries', function (Blueprint $table) {
            $table->string('id_produk')->nullable()->after('id');
            $table->string('nama_produk')->nullable()->after('id_produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kartu_stok_entries', function (Blueprint $table) {
            $table->dropColumn(['id_produk', 'nama_produk']);
        });
    }
};
