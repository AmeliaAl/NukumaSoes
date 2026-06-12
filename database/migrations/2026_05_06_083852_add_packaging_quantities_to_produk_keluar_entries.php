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
            $table->integer('jumlah_pouch')->nullable()->default(0);
            $table->integer('jumlah_ecofam')->nullable()->default(0);
            $table->integer('jumlah_family')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->dropColumn(['jumlah_pouch', 'jumlah_ecofam', 'jumlah_family']);
        });
    }
};
