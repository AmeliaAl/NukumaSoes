<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ACCOUNTS = [
        '111' => ['Kas (tunai)', 'aset', 'debit', 'nonaktif'],
        '112' => ['Bank BCA (bank)', 'aset', 'debit', 'nonaktif'],
        '301' => ['Modal', 'ekuitas', 'kredit', 'aktif'],
        '531' => ['Persediaan Awal Barang Dalam Proses', 'beban', 'debit', 'aktif'],
        '533' => ['Pemakaian Bahan Penolong', 'beban', 'debit', 'aktif'],
        '538' => ['Persediaan Akhir Barang Dalam Proses', 'beban', 'kredit', 'aktif'],
        '539' => ['Penutup Perkiraan Harga Pokok Produksi', 'beban', 'kredit', 'aktif'],
        '551' => ['Persediaan Awal Bahan Baku', 'beban', 'debit', 'aktif'],
        '552' => ['Pembelian Bahan Baku', 'beban', 'debit', 'nonaktif'],
        '553' => ['Ongkos Angkut Bahan Baku', 'beban', 'debit', 'nonaktif'],
        '554' => ['Potongan Pembelian Bahan Baku', 'beban', 'kredit', 'nonaktif'],
        '555' => ['Retur Pembelian Bahan Baku', 'beban', 'kredit', 'nonaktif'],
        '558' => ['Persediaan Akhir Bahan Baku', 'beban', 'kredit', 'aktif'],
        '559' => ['Penutup Perkiraan Pemakaian Bahan Baku', 'beban', 'kredit', 'aktif'],
        '600' => ['BIAYA OVERHEAD PRODUKSI', 'beban', 'debit', 'nonaktif'],
    ];

    private const REFERENCES = [
        ['jurnal_umum_detail', 'id_akun'],
        ['jurnal_detail', 'no_akun'],
        ['kategori_bop', 'id_akun'],
        ['modal', 'id_akun'],
        ['saldoawal', 'akun_id'],
        ['pembayaran_aset', 'id_akun'],
        ['pemeliharaan', 'id_akun'],
        ['pembayaran_utang_jangka_panjang', 'akun_id'],
        ['utang_jangka_panjang', 'akun_id'],
        ['utang_jangka_panjang', 'akun_debit_id'],
    ];

    public function up(): void
    {
        $codes = array_keys(self::ACCOUNTS);
        $accountIds = DB::table('akun')->whereIn('kode_akun', $codes)->pluck('id_akun');

        foreach (self::REFERENCES as [$table, $column]) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
                continue;
            }

            if (DB::table($table)->whereIn($column, $accountIds)->exists()) {
                throw new RuntimeException(
                    "COA di luar lingkup produksi tidak dapat dihapus karena masih digunakan pada {$table}.{$column}."
                );
            }
        }

        DB::table('akun')->whereIn('kode_akun', $codes)->delete();
    }

    public function down(): void
    {
        foreach (self::ACCOUNTS as $code => [$name, $type, $normalBalance, $status]) {
            DB::table('akun')->updateOrInsert(
                ['kode_akun' => $code],
                [
                    'nama_akun' => $name,
                    'tipe_akun' => $type,
                    'saldo_normal' => $normalBalance,
                    'saldo' => 0,
                    'status' => $status,
                    'keterangan' => 'Di luar lingkup modul produksi',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
};
