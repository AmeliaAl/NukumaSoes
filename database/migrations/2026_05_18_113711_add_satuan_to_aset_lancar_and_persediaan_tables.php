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
        // Tambah kolom satuan ke tabel aset_lancar
        Schema::table('aset_lancar', function (Blueprint $table) {
            $table->string('satuan')->default('Unit')->after('nama_barang');
        });

        // Tambah kolom satuan ke tabel persediaan
        Schema::table('persediaan', function (Blueprint $table) {
            $table->string('satuan')->default('Unit')->after('nama_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aset_lancar', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });

        Schema::table('persediaan', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
};

