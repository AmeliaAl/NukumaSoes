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
        Schema::table('tenaga_kerja', function (Blueprint $table) {
            $table->enum('jenis_tenaga', ['langsung', 'tidak_langsung'])
                  ->default('langsung')
                  ->after('jabatan')
                  ->comment('Langsung = terlibat langsung produksi, Tidak Langsung = support/overhead');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenaga_kerja', function (Blueprint $table) {
            $table->dropColumn('jenis_tenaga');
        });
    }
};