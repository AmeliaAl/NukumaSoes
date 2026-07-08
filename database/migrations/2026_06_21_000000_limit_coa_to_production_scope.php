<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PURCHASE_ACCOUNT_CODES = ['552', '553', '554', '555'];

    public function up(): void
    {
        DB::table('akun')
            ->where(function ($query) {
                $query->whereIn('kode_akun', self::PURCHASE_ACCOUNT_CODES)
                    ->orWhere('tipe_akun', 'pendapatan')
                    ->orWhereRaw('LOWER(nama_akun) LIKE ?', ['%penjualan%'])
                    ->orWhereRaw('LOWER(nama_akun) LIKE ?', ['%pembelian%']);
            })
            ->update([
                'status' => 'nonaktif',
                'keterangan' => 'Di luar lingkup modul produksi; dikelola oleh tim pembelian/penjualan.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('akun')
            ->whereIn('kode_akun', self::PURCHASE_ACCOUNT_CODES)
            ->update([
                'status' => 'aktif',
                'updated_at' => now(),
            ]);
    }
};
