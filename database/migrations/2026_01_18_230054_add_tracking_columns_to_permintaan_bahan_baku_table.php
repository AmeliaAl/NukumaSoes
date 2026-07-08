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
        Schema::table('permintaan_bahan_baku', function (Blueprint $table) {
            // Tambah kolom tracking TANPA menghapus yang lama
            $table->decimal('jumlah_diterima', 10, 2)->default(0)->after('jumlah_permintaan');
            $table->enum('status_penerimaan', ['belum', 'partial', 'completed'])->default('belum')->after('status_permintaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan_bahan_baku', function (Blueprint $table) {
            $table->dropColumn(['jumlah_diterima', 'status_penerimaan']);
        });
    }
};