<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah field jenis periode dan periode pembebanan ke tabel overheads.
     * - jenis_periode : harian | mingguan | bulanan
     * - periode_mulai : tanggal mulai pembebanan (sama dengan tanggal untuk harian)
     * - periode_akhir : tanggal akhir pembebanan (null untuk harian)
     *
     * Data lama (backward compatible) akan tetap terbaca;
     * jenis_periode default 'harian' dan periode_mulai diisi dari kolom tanggal.
     */
    public function up(): void
    {
        Schema::table('overheads', function (Blueprint $table) {
            $table->enum('jenis_periode', ['harian', 'mingguan', 'bulanan'])
                  ->default('harian')
                  ->after('tanggal');
            $table->date('periode_mulai')->nullable()->after('jenis_periode');
            $table->date('periode_akhir')->nullable()->after('periode_mulai');
        });

        // Backfill data lama: set periode_mulai = tanggal
        \Illuminate\Support\Facades\DB::table('overheads')->update([
            'periode_mulai' => \Illuminate\Support\Facades\DB::raw('tanggal'),
        ]);
    }

    public function down(): void
    {
        Schema::table('overheads', function (Blueprint $table) {
            $table->dropColumn(['jenis_periode', 'periode_mulai', 'periode_akhir']);
        });
    }
};
