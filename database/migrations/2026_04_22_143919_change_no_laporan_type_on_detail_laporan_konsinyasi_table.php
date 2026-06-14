<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_laporan_konsinyasi', function (Blueprint $table) {
            // drop foreign key kalau ada
            try {
                $table->dropForeign(['no_laporan']);
            } catch (\Throwable $e) {
                //
            }
        });

        DB::statement("
            ALTER TABLE detail_laporan_konsinyasi
            MODIFY no_laporan VARCHAR(255) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE detail_laporan_konsinyasi
            MODIFY no_laporan BIGINT UNSIGNED NOT NULL
        ");
    }
};