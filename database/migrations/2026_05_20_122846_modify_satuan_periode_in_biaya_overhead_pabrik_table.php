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
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE biaya_overhead_pabrik MODIFY COLUMN satuan_periode ENUM('per_batch', 'per_hari', 'per_minggu', 'per_bulan') NOT NULL DEFAULT 'per_batch'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE biaya_overhead_pabrik MODIFY COLUMN satuan_periode ENUM('per_batch', 'per_hari', 'per_bulan') NOT NULL DEFAULT 'per_batch'");
        });
    }
};
