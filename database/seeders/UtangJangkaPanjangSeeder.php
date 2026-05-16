<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UtangJangkaPanjang;
use App\Models\Akun;
use Carbon\Carbon;

class UtangJangkaPanjangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan akun sudah ada
        $akunKas = Akun::where('no_akun', 'like', '11%')->first();
        $akunUtang = Akun::where('header_akun', 2)->first();
        $akunAset = Akun::where('no_akun', 'like', '15%')->first();

        if (!$akunKas || !$akunUtang) {
            $this->command->error('Akun Kas atau Utang tidak ditemukan! Pastikan seeder Akun sudah dijalankan.');
            return;
        }

        // Contoh 1: Pinjaman Bank untuk Modal Kerja
        UtangJangkaPanjang::create([
            'tanggal' => Carbon::now()->subMonths(6),
            'nama_utang' => 'Pinjaman Bank BCA - Modal Kerja',
            'akun_id' => $akunUtang->id,
            'akun_debit_id' => $akunKas->id,
            'nominal' => 100000000,
            'jatuh_tempo' => Carbon::now()->addYears(5),
            'keterangan' => 'Pinjaman untuk modal kerja dengan bunga 8% per tahun',
        ]);

        // Contoh 2: Kredit Kendaraan (jika ada akun aset)
        if ($akunAset) {
            UtangJangkaPanjang::create([
                'tanggal' => Carbon::now()->subMonths(3),
                'nama_utang' => 'Kredit Kendaraan Operasional',
                'akun_id' => $akunUtang->id,
                'akun_debit_id' => $akunAset->id,
                'nominal' => 300000000,
                'jatuh_tempo' => Carbon::now()->addYears(4),
                'keterangan' => 'Kredit mobil operasional dengan DP 30%',
            ]);
        }

        // Contoh 3: Pinjaman Jangka Panjang yang Sudah Jatuh Tempo
        UtangJangkaPanjang::create([
            'tanggal' => Carbon::now()->subYears(6),
            'nama_utang' => 'Pinjaman Bank Mandiri (Sudah Jatuh Tempo)',
            'akun_id' => $akunUtang->id,
            'akun_debit_id' => $akunKas->id,
            'nominal' => 50000000,
            'jatuh_tempo' => Carbon::now()->subMonths(1), // Sudah lewat
            'keterangan' => 'Pinjaman yang sudah jatuh tempo, perlu segera dilunasi',
        ]);

        $this->command->info('✅ Seeder Utang Jangka Panjang berhasil! Jurnal otomatis telah dibuat.');
    }
}
