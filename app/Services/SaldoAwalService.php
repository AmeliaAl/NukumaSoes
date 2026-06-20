<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use App\Models\saldoawal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaldoAwalService
{
    /**
     * Buat jurnal dari saldo awal individual
     * Mirip dengan ModalService
     */
    public static function buatJurnal($saldoAwal): void
    {
        DB::transaction(function () use ($saldoAwal) {

            // Hapus jurnal lama jika ada (untuk update)
            if ($saldoAwal->jurnal_id) {
                $jurnalLama = Jurnal::find($saldoAwal->jurnal_id);
                if ($jurnalLama) {
                    $jurnalLama->details()->delete();
                    $jurnalLama->delete();
                }
            }

            // Ambil akun yang dipilih user
            $akun = Akun::find($saldoAwal->akun_id);
            if (!$akun) {
                throw new \Exception('Akun tidak ditemukan');
            }

            // Cek header akun (hanya 1, 2, 3 yang diperbolehkan)
            if (!in_array($akun->header_akun, [1, 2, 3])) {
                throw new \Exception('Hanya akun Aset, Kewajiban, dan Ekuitas yang diperbolehkan');
            }

            // Akun penyeimbang: Saldo Awal (399)
            $akunSaldoAwal = Akun::firstOrCreate(
                ['no_akun' => '399'],
                [
                    'nama_akun' => 'Saldo Awal',
                    'header_akun' => 3
                ]
            );

            // Tanggal: awal bulan dari periode saldo awal
            $tanggal = Carbon::create($saldoAwal->tahun, $saldoAwal->bulan, 1);

            // Buat jurnal header
            $jurnal = Jurnal::create([
                'tanggal'      => $tanggal,
                'no_referensi' => "SALDO-{$saldoAwal->id}",
                'deskripsi'    => "Saldo awal {$akun->nama_akun} - " . self::getNamaBulan($saldoAwal->bulan) . " {$saldoAwal->tahun}",
            ]);

            $nominal = abs($saldoAwal->nominal);

            // Tentukan Debit/Kredit berdasarkan saldo normal akun
            $isDebit = self::isAkunDebit($akun);

            if ($isDebit) {
                // Akun normal Debit (Aset, Prive)
                // D: Akun yang dipilih
                // K: Saldo Awal
                $jurnal->details()->create([
                    'no_akun'   => $akun->id,
                    'debit'     => $nominal,
                    'credit'    => 0,
                    'deskripsi' => "Saldo awal {$akun->nama_akun}",
                ]);

                $jurnal->details()->create([
                    'no_akun'   => $akunSaldoAwal->id,
                    'debit'     => 0,
                    'credit'    => $nominal,
                    'deskripsi' => 'Penyeimbang saldo awal',
                ]);
            } else {
                // Akun normal Kredit (Kewajiban, Ekuitas)
                // D: Saldo Awal
                // K: Akun yang dipilih
                $jurnal->details()->create([
                    'no_akun'   => $akunSaldoAwal->id,
                    'debit'     => $nominal,
                    'credit'    => 0,
                    'deskripsi' => 'Penyeimbang saldo awal',
                ]);

                $jurnal->details()->create([
                    'no_akun'   => $akun->id,
                    'debit'     => 0,
                    'credit'    => $nominal,
                    'deskripsi' => "Saldo awal {$akun->nama_akun}",
                ]);
            }

            // Simpan jurnal_id ke saldo awal
            $saldoAwal->updateQuietly(['jurnal_id' => $jurnal->id]);
        });
    }

    /**
     * Tentukan apakah akun masuk kategori debit normal
     * 
     * @param Akun $akun
     * @return bool
     */
    protected static function isAkunDebit(Akun $akun): bool
    {
        $headerAkun = $akun->header_akun;
        $namaAkun = strtolower($akun->nama_akun);

        // Akumulasi Penyusutan = Kontra Aset (Kredit)
        if (stripos($namaAkun, 'akumulasi') !== false || stripos($namaAkun, 'penyusutan') !== false) {
            return false;
        }

        // Prive = Kontra Ekuitas (tapi saldo normal Debit)
        if (stripos($namaAkun, 'prive') !== false) {
            return true;
        }

        // Header 1 = Aset (Debit normal)
        // Header 2 = Kewajiban (Kredit normal)
        // Header 3 = Ekuitas (Kredit normal)
        return $headerAkun == 1;
    }

    /**
     * Get nama bulan dalam bahasa Indonesia
     * 
     * @param int $bulan
     * @return string
     */
    protected static function getNamaBulan(int $bulan): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $namaBulan[$bulan] ?? 'Unknown';
    }

    /**
     * Hapus jurnal saat saldo awal dihapus
     * 
     * @param saldoawal $saldoAwal
     * @return void
     */
    public static function hapusJurnal(saldoawal $saldoAwal): void
    {
        if ($saldoAwal->jurnal_id) {
            $jurnal = Jurnal::find($saldoAwal->jurnal_id);
            if ($jurnal) {
                $jurnal->details()->delete();
                $jurnal->delete();
            }
        }
    }
}
