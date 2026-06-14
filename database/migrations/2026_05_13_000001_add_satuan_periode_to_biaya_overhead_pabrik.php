<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            $table->enum('satuan_periode', ['per_batch', 'per_hari', 'per_bulan'])
                  ->default('per_batch')
                  ->after('jenis_overhead');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            $table->dropColumn('satuan_periode');
        });
    }
};
