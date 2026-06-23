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
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->integer('stok_awal')->nullable()->after('jumlah_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->dropColumn('stok_awal');
        });
    }
};
