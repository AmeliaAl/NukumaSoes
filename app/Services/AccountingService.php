<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingService
{
    /**
     * Membuat Jurnal Umum beserta detail debit & kredit
     */
    public function createJurnal($tanggal, $keterangan, $referensiId, $tipeReferensi, $adminId, $entries)
    {
        // Pastikan total debit = total kredit
        $totalDebit = collect($entries)->sum('debit');
        $totalKredit = collect($entries)->sum('kredit');

        if (abs($totalDebit - $totalKredit) > 0.01) {
            Log::error("Jurnal tidak balance! Debit: $totalDebit, Kredit: $totalKredit. Referensi: $tipeReferensi - $referensiId");
            throw new \RuntimeException("Jurnal {$tipeReferensi} tidak seimbang.");
        }

        DB::beginTransaction();
        try {
            $jurnal = JurnalUmum::create([
                'tanggal' => $tanggal,
                'nomor_bukti' => JurnalUmum::generateNomorBukti(),
                'keterangan' => $keterangan,
                'id_referensi' => $referensiId,
                'tipe_referensi' => $tipeReferensi,
                'id_admin' => $adminId,
            ]);

            foreach ($entries as $entry) {
                // Hanya simpan jika nilai lebih dari 0
                if ($entry['debit'] > 0 || $entry['kredit'] > 0) {
                    JurnalUmumDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'id_akun' => $entry['id_akun'],
                        'debit' => $entry['debit'],
                        'kredit' => $entry['kredit'],
                    ]);

                    // Update Saldo Akun
                    $akun = Akun::find($entry['id_akun']);
                    if ($akun) {
                        if ($akun->saldo_normal === 'debit') {
                            $akun->saldo += $entry['debit'];
                            $akun->saldo -= $entry['kredit'];
                        } else {
                            $akun->saldo += $entry['kredit'];
                            $akun->saldo -= $entry['debit'];
                        }
                        $akun->save();
                    }
                }
            }

            DB::commit();
            return $jurnal;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Gagal membuat jurnal: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Helper: Mendapatkan ID Akun berdasarkan Kode
     */
    public function getAkunIdByKode($kode)
    {
        $akun = Akun::where('kode_akun', $kode)->where('status', 'aktif')->first();
        if (!$akun) {
            throw new \RuntimeException("Akun COA aktif dengan kode {$kode} tidak ditemukan.");
        }

        return $akun->id_akun;
    }

    // ==============================================================================
    // SIKLUS JURNAL PRODUKSI
    // ==============================================================================

    /**
     * 2. Pemakaian Bahan Baku (Masuk ke BDP)
     */
    public function recordPemakaianBahanBaku($pemakaian, $jobOrder)
    {
        if ($pemakaian->id_produk_wip) {
            // WIP dikonsumsi: debit 532 (BDP-BB) dan kredit 143 (Persediaan Setengah Jadi / WIP)
            $idAkunDebit = $this->getAkunIdByKode('532');
            $idAkunKredit = $this->getAkunIdByKode('143');
        } else {
            // Bahan baku biasa
            $bahan = \App\Models\BahanBaku::find($pemakaian->id_bahan);
            $jenisBahan = $bahan ? $bahan->jenis_bahan : 'langsung';

            if ($jenisBahan === 'tidak_langsung') {
                // Bahan tidak langsung (kemasan): debit 535 (BDP-BOP) dan kredit 142 (Persediaan Bahan Baku)
                $idAkunDebit = $this->getAkunIdByKode('535');
                $idAkunKredit = $this->getAkunIdByKode('142');
            } else {
                // Bahan langsung: debit 532 (BDP-BB) dan kredit 142 (Persediaan Bahan Baku)
                $idAkunDebit = $this->getAkunIdByKode('532');
                $idAkunKredit = $this->getAkunIdByKode('142');
            }
        }

        if (!$idAkunDebit || !$idAkunKredit) return false;

        $entries = [
            ['id_akun' => $idAkunDebit, 'debit' => $pemakaian->total_biaya, 'kredit' => 0],
            ['id_akun' => $idAkunKredit, 'debit' => 0, 'kredit' => $pemakaian->total_biaya],
        ];

        return $this->createJurnal(
            $pemakaian->tanggal_pemakaian,
            "Pemakaian Bahan Baku untuk Job: {$jobOrder->nomor_job}",
            $pemakaian->id_pemakaian,
            'pemakaian_bahan_baku',
            $pemakaian->id_admin,
            $entries
        );
    }

    /**
     * 3. Pembebanan Biaya Tenaga Kerja (Masuk ke BDP)
     */
    public function recordBiayaTenagaKerja($biayaTk, $jobOrder)
    {
        $tenaga = $biayaTk->tenagaKerja;
        $isLangsung = ($tenaga && $tenaga->jenis_tenaga === 'langsung');
        
        $kodeDebit = $isLangsung ? '561' : '535'; // 561: BDP-BTK (BTKL), 535: BDP-BOP (BTKTL)
        $idAkunDebit = $this->getAkunIdByKode($kodeDebit);
        $idAkunHutangGaji = $this->getAkunIdByKode('212'); // Hutang Gaji

        if (!$idAkunDebit || !$idAkunHutangGaji) return false;

        $entries = [
            ['id_akun' => $idAkunDebit, 'debit' => $biayaTk->total_biaya, 'kredit' => 0],
            ['id_akun' => $idAkunHutangGaji, 'debit' => 0, 'kredit' => $biayaTk->total_biaya],
        ];

        $jenisLabel = $isLangsung ? 'Langsung (BTKL)' : 'Tidak Langsung (BTKTL)';

        return $this->createJurnal(
            $biayaTk->tanggal_kerja,
            "Pembebanan Biaya Tenaga Kerja {$jenisLabel} untuk Job: {$jobOrder->nomor_job}",
            $biayaTk->id_biaya_tk,
            'biaya_tenaga_kerja',
            $biayaTk->id_admin,
            $entries
        );
    }

    /**
     * 4. Pembebanan Biaya Overhead Pabrik (Masuk ke BDP)
     */
    public function recordBiayaOverhead($overhead, $jobOrder)
    {
        $idAkunBDP = $this->getAkunIdByKode('535'); // Biaya Overhead Produksi (hasil) (Dulu 1-430)
        $idAkunBOPKredit = $this->getAkunIdByKode('699'); // Penutup Perkiraan Biaya & Beban (Kontra Akun Pembebanan) (Dulu 5-200)

        if (!$idAkunBDP || !$idAkunBOPKredit) return false;

        // Note: Gunakan ->nominal karena controller telah diupdate untuk field nominal
        $nominal = $overhead->nominal ?? $overhead->total_biaya; 

        $entries = [
            ['id_akun' => $idAkunBDP, 'debit' => $nominal, 'kredit' => 0],
            ['id_akun' => $idAkunBOPKredit, 'debit' => 0, 'kredit' => $nominal],
        ];

        return $this->createJurnal(
            $overhead->tanggal_overhead,
            "Pembebanan Biaya Overhead ({$overhead->jenis_overhead}) untuk Job: {$jobOrder->nomor_job}",
            $overhead->id_overhead, // pastikan nama primary key sesuai, di model adalah id_overhead
            'biaya_overhead_pabrik',
            $overhead->id_admin,
            $entries
        );
    }

    /**
     * 5. Job Order Selesai (Pindah dari BDP ke WIP / Produk Jadi)
     */
    public function recordBarangSelesai($jobOrder)
    {
        // Tentukan akun debit berdasarkan tujuan produksi
        $kodeDebit = $jobOrder->tujuan_produksi === 'stok_wip' ? '143' : '140'; // 143: WIP, 140: Brg Jadi
        $idAkunDebit = $this->getAkunIdByKode($kodeDebit);

        $idAkunBDP_BB = $this->getAkunIdByKode('532'); // Dulu 1-410
        $idAkunBDP_TK = $this->getAkunIdByKode('561'); // Dulu 1-420
        $idAkunBDP_BOP = $this->getAkunIdByKode('535'); // Dulu 1-430

        if (!$idAkunDebit || !$idAkunBDP_BB || !$idAkunBDP_TK || !$idAkunBDP_BOP) return false;

        $entries = [
            // Sisi Debit: Barang Jadi / WIP bertambah seluruh total biaya produksi
            ['id_akun' => $idAkunDebit, 'debit' => $jobOrder->total_biaya_produksi, 'kredit' => 0],
            
            // Sisi Kredit: BDP dikurangi (dipindah)
            ['id_akun' => $idAkunBDP_BB, 'debit' => 0, 'kredit' => $jobOrder->total_biaya_bahan],
            ['id_akun' => $idAkunBDP_TK, 'debit' => 0, 'kredit' => $jobOrder->total_biaya_tenaga_kerja],
            ['id_akun' => $idAkunBDP_BOP, 'debit' => 0, 'kredit' => $jobOrder->total_biaya_overhead],
        ];

        $keterangan = $jobOrder->tujuan_produksi === 'stok_wip' ? 'Penyelesaian WIP' : 'Penyelesaian Produk Jadi';

        return $this->createJurnal(
            $jobOrder->tanggal_selesai ?? now(),
            "{$keterangan} Job: {$jobOrder->nomor_job}",
            $jobOrder->id_permintaan_produksi,
            'permintaan_produksi',
            $jobOrder->id_admin,
            $entries
        );
    }

    /**
     * Pencatatan Jurnal Insentif Mingguan Tenaga Kerja
     */
    public function recordInsentifMingguan($tanggal, $nominal, $adminId, $tenagaKerja)
    {
        $idAkunInsentif = $this->getAkunIdByKode('705'); // Gaji Lainnya (insentif bonus)
        $idAkunHutangGaji = $this->getAkunIdByKode('212'); // Hutang lainnya

        if (!$idAkunInsentif || !$idAkunHutangGaji) return false;

        $entries = [
            ['id_akun' => $idAkunInsentif, 'debit' => $nominal, 'kredit' => 0],
            ['id_akun' => $idAkunHutangGaji, 'debit' => 0, 'kredit' => $nominal],
        ];

        return $this->createJurnal(
            $tanggal,
            "Pencatatan Insentif Mingguan untuk Pekerja: {$tenagaKerja->nama_tenaga}",
            $tenagaKerja->id_tenaga,
            'insentif_mingguan',
            $adminId,
            $entries
        );
    }
}
