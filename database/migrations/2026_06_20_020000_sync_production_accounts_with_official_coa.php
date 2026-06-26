<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $accounts = [
            '140' => ['Persediaan Barang Jadi', 'aset', 'debit'],
            '142' => ['Persediaan Bahan Baku', 'aset', 'debit'],
            '143' => ['Persediaan Barang dalam proses', 'aset', 'debit'],
            '212' => ['Hutang lainnya', 'kewajiban', 'kredit'],
            '531' => ['Persediaan Awal Barang Dalam Proses', 'beban', 'debit'],
            '532' => ['Pemakaian Bahan Baku', 'beban', 'debit'],
            '533' => ['Pemakaian Bahan Penolong', 'beban', 'debit'],
            '535' => ['Biaya Overhead Produksi (hasil)', 'beban', 'debit'],
            '538' => ['Persediaan Akhir Barang Dalam Proses', 'beban', 'kredit'],
            '539' => ['Penutup Perkiraan Harga Pokok Produksi', 'beban', 'kredit'],
            '551' => ['Persediaan Awal Bahan Baku', 'beban', 'debit'],
            '552' => ['Pembelian Bahan Baku', 'beban', 'debit'],
            '553' => ['Ongkos Angkut Bahan Baku', 'beban', 'debit'],
            '554' => ['Potongan Pembelian Bahan Baku', 'beban', 'kredit'],
            '555' => ['Retur Pembelian Bahan Baku', 'beban', 'kredit'],
            '558' => ['Persediaan Akhir Bahan Baku', 'beban', 'kredit'],
            '559' => ['Penutup Perkiraan Pemakaian Bahan Baku', 'beban', 'kredit'],
            '561' => ['Upah langsung', 'beban', 'debit'],
            '601' => ['Gaji Karyawan', 'beban', 'debit'],
            '612' => ['Biaya Gas', 'beban', 'debit'],
            '613' => ['Biaya Alat Tulis Kantor produksi', 'beban', 'debit'],
            '616' => ['Biaya Peralatan dan Mesin Perlengkapan prod', 'beban', 'debit'],
            '619' => ['Biaya Transportasi (Beban ongkir)', 'beban', 'debit'],
            '620' => ['Biaya Bahan penolong', 'beban', 'debit'],
            '653' => ['Beban Penyusutan Kendaraan pabrik', 'beban', 'debit'],
            '699' => ['Penutup Perkiraan Biaya & Beban Pabrikasi', 'beban', 'kredit'],
            '705' => ['Gaji Lainnya (insentif bonus)', 'beban', 'debit'],
            '712' => ['Biaya Listrik, air', 'beban', 'debit'],
        ];

        foreach ($accounts as $code => [$name, $type, $normalBalance]) {
            $existing = DB::table('akun')->where('kode_akun', $code)->first();
            if ($existing) {
                DB::table('akun')->where('kode_akun', $code)->update([
                    'nama_akun' => $name,
                    'tipe_akun' => $type,
                    'saldo_normal' => $normalBalance,
                    'status' => 'aktif',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('akun')->insert([
                    'kode_akun' => $code,
                    'nama_akun' => $name,
                    'tipe_akun' => $type,
                    'saldo_normal' => $normalBalance,
                    'saldo' => 0,
                    'status' => 'aktif',
                    'keterangan' => 'COA resmi - lingkup produksi',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Kode 600 adalah header kelompok, bukan akun yang boleh diposting.
        DB::table('akun')->where('kode_akun', '600')->update([
            'nama_akun' => 'BIAYA OVERHEAD PRODUKSI',
            'status' => 'nonaktif',
            'updated_at' => now(),
        ]);

        // COA resmi menggabungkan listrik dan air pada kode 712.
        $electricity = DB::table('akun')->where('kode_akun', '712')->first();
        $water = DB::table('akun')->where('kode_akun', '713')->first();
        if ($electricity && $water) {
            DB::table('jurnal_umum_detail')
                ->where('id_akun', $water->id_akun)
                ->update(['id_akun' => $electricity->id_akun]);

            DB::table('akun')->where('id_akun', $electricity->id_akun)->update([
                'saldo' => (float) $electricity->saldo + (float) $water->saldo,
                'updated_at' => now(),
            ]);
            DB::table('akun')->where('id_akun', $water->id_akun)->delete();
        }
    }

    public function down(): void
    {
        DB::table('akun')->where('kode_akun', '600')->update(['status' => 'aktif']);
        DB::table('akun')->where('kode_akun', '712')->update(['nama_akun' => 'Biaya Listrik']);

        if (DB::table('akun')->where('kode_akun', '713')->doesntExist()) {
            DB::table('akun')->insert([
                'kode_akun' => '713',
                'nama_akun' => 'Biaya Air',
                'tipe_akun' => 'beban',
                'saldo_normal' => 'debit',
                'saldo' => 0,
                'status' => 'aktif',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
