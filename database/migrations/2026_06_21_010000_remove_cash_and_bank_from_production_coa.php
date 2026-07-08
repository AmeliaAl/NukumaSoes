<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const CASH_AND_BANK_CODES = ['111', '112'];

    public function up(): void
    {
        DB::table('akun')
            ->whereIn('kode_akun', self::CASH_AND_BANK_CODES)
            ->update([
                'status' => 'nonaktif',
                'keterangan' => 'Di luar lingkup modul produksi; dikelola oleh sistem keuangan tim terkait.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('akun')
            ->whereIn('kode_akun', self::CASH_AND_BANK_CODES)
            ->update([
                'status' => 'aktif',
                'updated_at' => now(),
            ]);
    }
};
