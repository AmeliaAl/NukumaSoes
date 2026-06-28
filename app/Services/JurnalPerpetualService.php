<?php

namespace App\Services;

use App\Models\akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\LaporanKonsinyasi;
use App\Models\PembayaranTagihanKonsinyasi;
use App\Models\PenjualanNonKonsinyasi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Log;

class JurnalPerpetualService
{
    // ─────────────────────────────────────────────
    // NON-KONSINYASI
    // ─────────────────────────────────────────────

    /**
     * Jurnal perpetual penjualan non-konsinyasi — satu nomor jurnal.
     *
     * Struktur jurnal_detail dalam satu jurnal_umum:
     *
     * Tunai (tanpa diskon):
     *   D  Kas/Bank/Toko Online     = total
     *   K  Penjualan                = total
     *   D  Harga Pokok Penjualan    = total_hpp
     *   K  Persediaan Barang Jadi   = total_hpp
     *
     * Kredit (dengan diskon):
     *   D  Piutang Usaha            = total (setelah diskon)
     *   D  Potongan Penjualan       = diskon
     *   K  Penjualan                = total + diskon (sebelum diskon)
     *   D  Harga Pokok Penjualan    = total_hpp
     *   K  Persediaan Barang Jadi   = total_hpp
     *
     * Total debit = total kredit selalu terpenuhi.
     */
    public static function penjualanNonKonsinyasi(PenjualanNonKonsinyasi $penjualan): void
    {
        $penjualan->refresh();

        // Hitung total_bruto = SUM(qty × harga) dari detail — sebelum diskon apapun
        $totalBruto = (int) $penjualan->detailPenjualan()
            ->selectRaw('SUM(qty * harga) as bruto')
            ->value('bruto');

        // Hitung total diskon detail = SUM(detail.diskon)
        $diskonDetail = (int) $penjualan->detailPenjualan()->sum('diskon');

        // Diskon header/faktur
        $diskonHeader = (int) ($penjualan->diskon ?? 0);

        // Total potongan penjualan = diskon detail + diskon header
        $totalPotongan = $diskonDetail + $diskonHeader;

        // Kas/Piutang yang diterima = total_bruto - total_potongan
        $kasPiutang = max($totalBruto - $totalPotongan, 0);

        $totalHpp = (float) ($penjualan->total_hpp ?? 0);

        if ($totalBruto <= 0) {
            return;
        }

        $referensi = 'Jurnal Penjualan Non Konsinyasi ' . $penjualan->no_invoice;

        // Hapus jurnal lama (penjualan + HPP) jika ada
        foreach (['Jurnal Penjualan Non Konsinyasi ', 'Jurnal HPP Non Konsinyasi '] as $prefix) {
            $lama = Jurnal::where('deskripsi', $prefix . $penjualan->no_invoice)->first();
            if ($lama) {
                $lama->details()->delete();
                $lama->delete();
            }
        }

        $akunDebit      = self::getAkunDebitPenjualan($penjualan);
        $akunPenjualan  = akun::where('no_akun', '401')->firstOrFail();
        $akunHpp        = akun::where('no_akun', '500')->firstOrFail();
        $akunPersediaan = akun::where('no_akun', '143')->firstOrFail();

        $jurnal = Jurnal::create([
            'tanggal'    => $penjualan->tanggal,
            'deskripsi' => $referensi,
        ]);

        // ── Jurnal Penjualan ─────────────────────────────────────────────

        // 1. D: Kas / Piutang / Toko Online = total_bruto - total_potongan
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun'   => $akunDebit->id,
            'deskripsi' => 'Penjualan ' . $penjualan->no_invoice,
            'debit'     => $kasPiutang,
            'credit'    => 0,
        ]);

        // 2. D: Potongan Penjualan = diskon detail + diskon header (hanya jika ada)
        if ($totalPotongan > 0) {
            $akunPotongan = akun::where('no_akun', '403')->firstOrFail();
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunPotongan->id,
                'deskripsi' => 'Potongan Penjualan',
                'debit'     => $totalPotongan,
                'credit'    => 0,
            ]);
        }

        // 3. K: Penjualan = total_bruto (sebelum semua diskon)
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun'   => $akunPenjualan->id,
            'deskripsi' => 'Penjualan',
            'debit'     => 0,
            'credit'    => $totalBruto,
        ]);

        // ── Jurnal HPP (dalam jurnal yang sama) ──────────────────────────

        if ($totalHpp > 0) {
            // 4. D: Harga Pokok Penjualan
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunHpp->id,
                'deskripsi' => 'HPP',
                'debit'     => $totalHpp,
                'credit'    => 0,
            ]);

            // 5. K: Persediaan Barang Jadi
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunPersediaan->id,
                'deskripsi' => 'Persediaan',
                'debit'     => 0,
                'credit'    => $totalHpp,
            ]);
        }
    }

    /**
     * Tentukan akun debit untuk jurnal penjualan non-konsinyasi.
     * - Marketplace → Toko Online (113)
     * - Kredit      → Piutang Usaha (130)
     * - Lainnya     → Bank (112)
     */
    protected static function getAkunDebitPenjualan(PenjualanNonKonsinyasi $penjualan): akun
    {
        if ($penjualan->jenis_penjualan === 'MARKETPLACE') {
            return akun::where('no_akun', '113')->firstOrFail();
        }

        if (($penjualan->jenis_pembayaran ?? null) === 'kredit') {
            return akun::where('no_akun', '130')->firstOrFail();
        }

        return akun::where('no_akun', '112')->firstOrFail();
    }

    /**
     * Jurnal saat pembayaran piutang non-konsinyasi diterima.
     *
     * Selalu: Bank (D) / Piutang Usaha (K)
     * Penjualan tunai tidak perlu jurnal pembayaran.
     */
    public static function pembayaranNonKonsinyasi(Pembayaran $pembayaran): void
    {
        $nominal   = (int) ($pembayaran->jumlah_bayar ?? 0);
        $penjualan = $pembayaran->penjualan;

        if ($nominal <= 0) {
            return;
        }

        if ($penjualan && $penjualan->jenis_pembayaran === 'tunai') {
            return;
        }

        $referensi = 'Jurnal Pembayaran Piutang ' . $pembayaran->kode_pembayaran;

        $jurnalLama = Jurnal::where('deskripsi', $referensi)->first();
        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        $akunBank    = self::getAkunDebitPembayaran($pembayaran);
        $akunPiutang = akun::where('no_akun', '130')->firstOrFail();

        $jurnal = Jurnal::create([
            'tanggal'    => $pembayaran->tanggal_bayar ?? now(),
            'deskripsi' => $referensi,
        ]);

        // 1. D: Bank
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun'   => $akunBank->id,
            'deskripsi' => 'Pembayaran Piutang',
            'debit'     => $nominal,
            'credit'    => 0,
        ]);

        // 2. K: Piutang Usaha
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun'   => $akunPiutang->id,
            'deskripsi' => 'Piutang Usaha',
            'debit'     => 0,
            'credit'    => $nominal,
        ]);
    }

    protected static function getAkunDebitPembayaran(Pembayaran $pembayaran): akun
    {
        return akun::where('no_akun', '112')->firstOrFail();
    }

    // ─────────────────────────────────────────────
    // KONSINYASI
    // ─────────────────────────────────────────────

    /**
     * Jurnal saat Laporan Konsinyasi dibuat/diupdate.
     */
    public static function laporanKonsinyasi(LaporanKonsinyasi $laporan): void
    {
        self::laporanKonsinyasiDenganNominal($laporan, (int) ($laporan->total_laporan ?? 0));
    }

    /**
     * Jurnal perpetual penjualan konsinyasi — satu nomor jurnal.
     *
     * Struktur jurnal_detail dalam satu jurnal_umum:
     *
     * Tanpa diskon:
     *   D  Piutang Usaha            = nominal
     *   K  Penjualan                = nominal
     *   D  Harga Pokok Penjualan    = total_hpp
     *   K  Persediaan Barang Jadi   = total_hpp
     *
     * Dengan diskon proporsional:
     *   D  Piutang Usaha            = nominal (setelah diskon)
     *   D  Potongan Penjualan       = diskon proporsional
     *   K  Penjualan                = nominal + diskon proporsional
     *   D  Harga Pokok Penjualan    = total_hpp
     *   K  Persediaan Barang Jadi   = total_hpp
     */
    public static function laporanKonsinyasiDenganNominal(LaporanKonsinyasi $laporan, int $nominal): void
    {
        if ($nominal <= 0) {
            return;
        }

        // Refresh + load relasi agar total_hpp dan penjualanKonsinyasi terbaca
        $laporan->refresh();
        $laporan->load('penjualanKonsinyasi', 'detailLaporan');

        $totalHpp = (float) ($laporan->total_hpp ?? 0);

        $diskonProporsional = self::hitungDiskonProporsionalKonsinyasi($laporan, $nominal);
        $totalBruto         = $nominal + $diskonProporsional;
        $kasPiutang         = $nominal;

        $referensi = 'Jurnal Laporan Konsinyasi ' . $laporan->no_laporan;

        // Hapus jurnal lama (penjualan + HPP) jika ada
        foreach (['Jurnal Laporan Konsinyasi ', 'Jurnal HPP Konsinyasi '] as $prefix) {
            $lama = Jurnal::where('deskripsi', $prefix . $laporan->no_laporan)->first();
            if ($lama) {
                $lama->details()->delete();
                $lama->delete();
            }
        }

        $akunPiutang    = akun::where('no_akun', '130')->firstOrFail();
        $akunPenjualan  = akun::where('no_akun', '401')->firstOrFail();
        $akunHpp        = akun::where('no_akun', '500')->firstOrFail();
        $akunPersediaan = akun::where('no_akun', '143')->firstOrFail();

        $jurnal = Jurnal::create([
            'tanggal'    => $laporan->tanggal_laporan ?? $laporan->created_at ?? now(),
            'deskripsi' => $referensi,
        ]);

        // D: Piutang Usaha
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun'   => $akunPiutang->id,
            'deskripsi' => 'Piutang Usaha',
            'debit'     => $kasPiutang,
            'credit'    => 0,
        ]);

        // D: Potongan Penjualan (hanya jika ada diskon proporsional)
        if ($diskonProporsional > 0) {
            $akunPotongan = akun::where('no_akun', '403')->firstOrFail();
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunPotongan->id,
                'deskripsi' => 'Potongan Penjualan',
                'debit'     => $diskonProporsional,
                'credit'    => 0,
            ]);
        }

        // K: Penjualan = total_bruto
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun'   => $akunPenjualan->id,
            'deskripsi' => 'Penjualan',
            'debit'     => 0,
            'credit'    => $totalBruto,
        ]);

        // D: HPP + K: Persediaan (hanya jika ada HPP)
        if ($totalHpp > 0) {
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunHpp->id,
                'deskripsi' => 'HPP',
                'debit'     => $totalHpp,
                'credit'    => 0,
            ]);

            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunPersediaan->id,
                'deskripsi' => 'Persediaan',
                'debit'     => 0,
                'credit'    => $totalHpp,
            ]);
        }

        \Illuminate\Support\Facades\Log::info("Jurnal konsinyasi dibuat: {$referensi} | nominal={$nominal} | hpp={$totalHpp}");
    }

    /**
     * Hitung diskon proporsional untuk satu laporan konsinyasi.
     * diskon_laporan = diskon_total × (total_laporan / total_konsinyasi)
     */
    protected static function hitungDiskonProporsionalKonsinyasi(
        LaporanKonsinyasi $laporan,
        int $nominalLaporan
    ): int {
        $penjualan = $laporan->penjualanKonsinyasi;

        if (! $penjualan) {
            return 0;
        }

        $diskonHeader    = (int) ($penjualan->diskon ?? 0);
        $diskonDetail    = (int) $penjualan->detailKonsinyasi()->sum('diskon');
        $diskonTotal     = $diskonHeader + $diskonDetail;

        $totalKonsinyasi = (int) ($penjualan->total_konsinyasi ?? 0);

        if ($diskonTotal <= 0 || $totalKonsinyasi <= 0) {
            return 0;
        }

        return (int) round($diskonTotal * ($nominalLaporan / $totalKonsinyasi));
    }

    /**
     * Jurnal saat pembayaran tagihan konsinyasi diterima.
     * Bank (D) / Piutang Usaha (K)
     */
    public static function pembayaranTagihanKonsinyasi(PembayaranTagihanKonsinyasi $pembayaran): void
    {
        $nominal = (int) ($pembayaran->jumlah_bayar ?? 0);

        if ($nominal <= 0) {
            return;
        }

        $noTagihan = $pembayaran->tagihanKonsinyasi?->no_tagihan ?? $pembayaran->tagihan_konsinyasi_id;
        $referensi = 'Jurnal Pembayaran Konsinyasi ' . $noTagihan . '-' . $pembayaran->id;

        $jurnalLama = Jurnal::where('deskripsi', $referensi)->first();
        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        $akunBank    = self::getAkunBankPembayaranKonsinyasi($pembayaran);
        $akunPiutang = akun::where('no_akun', '130')->firstOrFail();

        $jurnal = Jurnal::create([
            'tanggal'    => $pembayaran->tanggal_bayar ?? now(),
            'deskripsi' => $referensi,
        ]);

        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun'   => $akunBank->id,
            'deskripsi' => 'Bank',
            'debit'     => $nominal,
            'credit'    => 0,
        ]);

        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun'   => $akunPiutang->id,
            'deskripsi' => 'Piutang Usaha',
            'debit'     => 0,
            'credit'    => $nominal,
        ]);
    }

    protected static function getAkunBankPembayaranKonsinyasi(PembayaranTagihanKonsinyasi $pembayaran): akun
    {
        return akun::where('no_akun', '112')->firstOrFail();
    }

    // ─────────────────────────────────────────────
    // SALDO AWAL
    // ─────────────────────────────────────────────

    public static function saldoAwal(\App\Models\SaldoAwal $saldoAwal): void
    {
        $saldoAwal->load('akun');
        $nominal = (int) $saldoAwal->nominal;

        if ($nominal <= 0) {
            return;
        }

        $referensi = 'Saldo awal ' . $saldoAwal->akun->nama_akun . ' periode ' . $saldoAwal->bulan . '-' . $saldoAwal->tahun;

        // Hapus jurnal lama (jika ada update)
        $jurnalLama = Jurnal::where('deskripsi', $referensi)->first();
        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        // Cari atau buat akun "Saldo Awal" penyeimbang
        $akunSaldoAwal = akun::firstOrCreate(
            ['nama_akun' => 'Saldo Awal'],
            [
                'no_akun' => '399',
                'header_akun' => 3
            ]
        );

        $tanggal = \Carbon\Carbon::createFromDate($saldoAwal->tahun, $saldoAwal->bulan, 1)->format('Y-m-d');

        $jurnal = Jurnal::create([
            'tanggal'    => $tanggal,
            'deskripsi' => $referensi,
        ]);

        $kodeAkun = $saldoAwal->akun->no_akun;
        $awalKode = substr((string)$kodeAkun, 0, 1);

        if (in_array($awalKode, ['1', '5', '6', '8', '9'])) {
            // Normal Debit
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $saldoAwal->akun->id,
                'deskripsi' => 'Saldo Awal',
                'debit'     => $nominal,
                'credit'    => 0,
            ]);
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunSaldoAwal->id,
                'deskripsi' => 'Saldo Awal',
                'debit'     => 0,
                'credit'    => $nominal,
            ]);
        } else {
            // Normal Kredit
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $akunSaldoAwal->id,
                'deskripsi' => 'Saldo Awal',
                'debit'     => $nominal,
                'credit'    => 0,
            ]);
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun'   => $saldoAwal->akun->id,
                'deskripsi' => 'Saldo Awal',
                'debit'     => 0,
                'credit'    => $nominal,
            ]);
        }
    }
}