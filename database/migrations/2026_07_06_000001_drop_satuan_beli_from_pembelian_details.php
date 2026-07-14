<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus field satuan_beli dari pembelian_details.
     * Field ini sudah tidak digunakan sesuai revisi struktur Master Bahan Baku.
     */
    public function up(): void
    {
        Schema::table('pembelian_details', function (Blueprint $table) {
            $table->dropColumn('satuan_beli');
        });
    }

    public function down(): void
    {
        Schema::table('pembelian_details', function (Blueprint $table) {
            $table->string('satuan_beli')->nullable()->after('qty');
        });
    }
};
