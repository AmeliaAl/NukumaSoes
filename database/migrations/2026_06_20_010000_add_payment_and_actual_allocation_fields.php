<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->string('nomor_pembayaran', 100)->nullable()->unique()->after('nomor_bukti');
        });

        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            $table->boolean('is_alokasi_aktual')->default(false)->after('id_jurnal_aktual');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            $table->dropColumn('is_alokasi_aktual');
        });

        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->dropUnique(['nomor_pembayaran']);
            $table->dropColumn('nomor_pembayaran');
        });
    }
};
