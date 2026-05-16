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
            $table->string('produk_expired')->nullable()->after('nama_akun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluaran_entries', function (Blueprint $table) {
            $table->dropColumn('produk_expired');
        });
    }
};
