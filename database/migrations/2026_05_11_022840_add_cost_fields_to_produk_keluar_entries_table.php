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
            $table->integer('jumlah_pack_keluar')->nullable();
            $table->decimal('harga_pokok_per_pack', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->dropColumn(['jumlah_pack_keluar', 'harga_pokok_per_pack']);
        });
    }
};
