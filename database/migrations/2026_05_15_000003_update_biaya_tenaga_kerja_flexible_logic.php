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
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            // Drop kolom lama (jika ada)
            if (Schema::hasColumn('biaya_tenaga_kerja', 'hari_tidak_masuk')) {
                $table->dropColumn('hari_tidak_masuk');
            }

            // Tambah kolom baru
            $table->decimal('jam_absen', 5, 2)->default(0)->after('minggu_kerja');
            $table->decimal('nominal_potongan', 15, 2)->default(0)->after('upah_per_minggu');
            $table->decimal('nominal_lembur', 15, 2)->default(0)->after('nominal_potongan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            $table->dropColumn(['jam_absen', 'nominal_potongan', 'nominal_lembur']);
            $table->unsignedTinyInteger('hari_tidak_masuk')->default(0);
        });
    }
};
