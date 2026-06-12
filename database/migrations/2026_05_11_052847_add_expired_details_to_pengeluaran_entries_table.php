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
        Schema::table('pengeluaran_entries', function (Blueprint $table) {
            $table->integer('jumlah_expired')->nullable()->after('produk_expired');
            $table->decimal('harga_pokok_per_pack', 15, 2)->nullable()->after('jumlah_expired');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluaran_entries', function (Blueprint $table) {
            $table->dropColumn(['jumlah_expired', 'harga_pokok_per_pack']);
        });
    }
};
