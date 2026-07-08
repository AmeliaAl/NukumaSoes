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
            $table->renameColumn('minggu_kerja', 'hari_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            $table->renameColumn('hari_kerja', 'minggu_kerja');
        });
    }
};
