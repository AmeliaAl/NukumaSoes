<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            // Jumlah hari tidak masuk dalam periode (maks 5 hari per minggu)
            $table->unsignedTinyInteger('hari_tidak_masuk')
                  ->default(0)
                  ->after('minggu_kerja')
                  ->comment('Jumlah hari tidak masuk. Potongan = hari_tidak_masuk × upah_per_minggu / 5');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            $table->dropColumn('hari_tidak_masuk');
        });
    }
};
