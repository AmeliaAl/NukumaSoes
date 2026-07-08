<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            // Jumlah batch untuk kalkulasi BTK per batch
            $table->unsignedSmallInteger('jumlah_batch')
                  ->default(1)
                  ->after('minggu_kerja')
                  ->comment('Jumlah batch yang dikerjakan, kalkulasi: upah_per_minggu × minggu_kerja × jumlah_batch');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            $table->dropColumn('jumlah_batch');
        });
    }
};
