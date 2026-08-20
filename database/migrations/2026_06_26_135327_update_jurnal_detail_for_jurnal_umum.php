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
        Schema::table('jurnal_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('jurnal_detail', 'jurnal_umum_id')) {
                $table->unsignedBigInteger('jurnal_umum_id')->nullable()->after('id_jurnal');
                // Optional: add foreign key
                // $table->foreign('jurnal_umum_id')->references('id')->on('jurnal_umum')->onDelete('cascade');
            }
        });
        // Make id_jurnal nullable natively
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE jurnal_detail MODIFY id_jurnal BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_detail', function (Blueprint $table) {
            if (Schema::hasColumn('jurnal_detail', 'jurnal_umum_id')) {
                $table->dropColumn('jurnal_umum_id');
            }
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE jurnal_detail MODIFY id_jurnal BIGINT UNSIGNED NOT NULL');
    }
};
