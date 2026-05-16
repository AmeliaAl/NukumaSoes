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
     * Generate jurnal saldo awal untuk periode tertentu
     * 
     * @param int $bulan
     * @param int $tahun
     * @return Jurnal|null
     */
    public static function generateJurnalSaldoAwal(int $bulan, int $tahun): ?Jurnal
    {
        return DB::transaction(function () use ($bulan, $tahun) {
            // 1. Cek apakah sudah ada jurnal saldo awal untuk periode ini
            $existingJurnal = Jurnal::where('no_referensi', "SALDO-AWAL-{$bulan}-{$tahun}")
                ->first();

            if ($existingJurnal) {
                // Jurnal sudah ada, skip
                return $existingJurnal;
            }

            // 2. Ambil semua saldo awal untuk periode ini
            $saldoAwals = saldoawal::with('akun')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->whereNull('jurnal_id') // Hanya yang belum punya jurnal
                ->get();

            if ($saldoAwals->isEmpty()) {
                return null;
            }

            // 3. Buat header jurnal
            $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            
            $jurnal = Jurnal::create([
                'tanggal' => $tanggalAwal,
                'no_referensi' => "SALDO-AWAL-{$bulan}-{$tahun}",
                'deskripsi' => "Jurnal Saldo Awal - " . self::getNamaBulan($bulan) . " {$tahun}",
            ]);

            $totalDebit = 0;
            $totalKredit = 0;

            // 4. Loop semua saldo awal dan buat jurnal detail
            foreach ($saldoAwals as $saldoAwal) {
                $akun = $saldoAwal->akun;
                $nominal = abs($saldoAwal->nominal);

                // Tentukan posisi debit/kredit berdasarkan saldo normal akun
                $isDebit = self::isAkunDebit($akun);

                if ($isDebit) {
                    // Aset, Beban, Prive → Debit
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id,
                        'no_akun' => $akun->id,
                        'deskripsi' => "Saldo Awal {$akun->nama_akun}",
                        'debit' => $nominal,
                        'credit' => 0,
                    ]);
                    $totalDebit += $nominal;
                } else {
                    // Kewajiban, Ekuitas, Pendapatan, Akumulasi Penyusutan → Kredit
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id,
                        'no_akun' => $akun->id,
                        'deskripsi' => "Saldo Awal {$akun->nama_akun}",
                        'debit' => 0,
                        'credit' => $nominal,
                    ]);
                    $totalKredit += $nominal;
                }

                // Update saldo awal dengan jurnal_id
                $saldoAwal->update(['jurnal_id' => $jurnal->id]);
            }

            // 5. Balance jurnal dengan akun Modal jika tidak balance
            if ($totalDebit != $totalKredit) {
                $selisih = abs($totalDebit - $totalKredit);
                $akunModal = self::getAkunModal();

                if (!$akunModal) {
                    throw new \Exception('Akun Modal tidak ditemukan! Pastikan ada akun dengan header_akun = 3 dan nama mengandung "Modal"');
                }

                if ($totalDebit > $totalKredit) {
                    // Debit lebih besar → tambahkan ke Kredit Modal
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id,
                        'no_akun' => $akunModal->id,
                        'deskripsi' => "Penyesuaian Saldo Awal (Modal)",
                        'debit' => 0,
                        'credit' => $selisih,
                    ]);
                    $totalKredit += $selisih;
                } else {
                    // Kredit lebih besar → tambahkan ke Debit Modal
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id,
                        'no_akun' => $akunModal->id,
                        'deskripsi' => "Penyesuaian Saldo Awal (Modal)",
                        'debit' => $selisih,
                        'credit' => 0,
                    ]);
                    $totalDebit += $selisih;
                }
            }

            // 6. Validasi final balance
            if ($totalDebit != $totalKredit) {
                throw new \Exception("Jurnal tidak balance! Debit: {$totalDebit}, Kredit: {$totalKredit}");
            }

            return $jurnal;
        });
    }

    /**
     * Tentukan apakah akun masuk kategori debit
     * 
     * @param Akun $akun
     * @return bool
     */
    protected static function isAkunDebit(Akun $akun): bool
    {
        // Header 1 = Aset (kecuali Akumulasi Penyusutan)
        // Header 5, 6, 7 = Beban
        // Prive (cek dari nama akun)
        
        $headerAkun = $akun->header_akun;
        $namaAkun = strtolower($akun->nama_akun);

        // Akumulasi Penyusutan = Kontra Aset (Kredit)
        if (stripos($namaAkun, 'akumulasi') !== false || stripos($namaAkun, 'penyusutan') !== false) {
            return false;
        }

        // Prive = Kontra Ekuitas (Debit, tapi dikurangi dari ekuitas)
        if (stripos($namaAkun, 'prive') !== false) {
            return true;
        }

        // Aset (1) dan Beban (5, 6, 7) = Debit
        if (in_array($headerAkun, [1, 5, 6, 7])) {
            return true;
        }

        // Kewajiban (2), Ekuitas (3), Pendapatan (4) = Kredit
        return false;
    }

    /**
     * Cari akun Modal untuk penyesuaian
     * 
     * @return Akun|null
     */
    protected static function getAkunModal(): ?Akun
    {
        // Cari akun dengan header_akun = 3 (Ekuitas) dan nama mengandung "Modal"
        return Akun::where('header_akun', 3)
            ->where(function($q) {
                $q->where('nama_akun', 'like', '%Modal%')
                  ->orWhere('nama_akun', 'like', '%Equity%')
                  ->orWhere('nama_akun', 'like', '%Capital%');
            })
            ->first();
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
     * Generate jurnal untuk saldo awal yang baru dibuat
     * 
     * @param saldoawal $saldoAwal
     * @return void
     */
    public static function generateJurnalForNewSaldoAwal(saldoawal $saldoAwal): void
    {
        // Generate jurnal untuk periode saldo awal ini
        self::generateJurnalSaldoAwal($saldoAwal->bulan, $saldoAwal->tahun);
    }

    /**
     * Hapus jurnal saldo awal jika semua saldo awal dihapus
     * 
     * @param int $bulan
     * @param int $tahun
     * @return void
     */
    public static function deleteJurnalSaldoAwalIfEmpty(int $bulan, int $tahun): void
    {
        // Cek apakah masih ada saldo awal untuk periode ini
        $count = saldoawal::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->count();

        if ($count === 0) {
            // Hapus jurnal jika tidak ada saldo awal lagi
            $jurnal = Jurnal::where('no_referensi', "SALDO-AWAL-{$bulan}-{$tahun}")
                ->first();

            if ($jurnal) {
                $jurnal->delete(); // Cascade delete jurnal_detail
            }
        }
    }
}
