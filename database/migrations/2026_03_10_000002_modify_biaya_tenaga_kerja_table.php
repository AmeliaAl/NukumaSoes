<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            $table->renameColumn('jam_kerja', 'minggu_kerja');
            $table->renameColumn('upah_per_jam', 'upah_per_minggu');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_tenaga_kerja', function (Blueprint $table) {
            $table->renameColumn('minggu_kerja', 'jam_kerja');
            $table->renameColumn('upah_per_minggu', 'upah_per_jam');
        });
    }
};
