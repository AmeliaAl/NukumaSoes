<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            // Jumlah batch untuk kalkulasi BOP per batch
            $table->unsignedSmallInteger('jumlah_batch')
                  ->default(1)
                  ->after('jenis_overhead')
                  ->comment('Jumlah batch, kalkulasi total: nominal × jumlah_batch');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            $table->dropColumn('jumlah_batch');
        });
    }
};
