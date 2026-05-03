<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\JurnalUmum;
use App\Models\JurnalDetail;
use App\Models\LaporanKonsinyasi;
use App\Models\PembayaranTagihanKonsinyasi;
use App\Models\PenjualanNonKonsinyasi;
use App\Models\Pembayaran;

class JurnalPerpetualService
{
    // ─────────────────────────────────────────────
    // NON-KONSINYASI
    // ─────────────────────────────────────────────

    /**
     * Jurnal saat penjualan non-konsinyasi dibuat.
     * Tunai/Transfer : Bank (D)     / Penjualan (K)
     * Marketplace    : Toko Online (D) / Penjualan (K)
     * Kredit         : Piutang (D)  / Penjualan (K)
     */
    public static function penjualanNonKonsinyasi(PenjualanNonKonsinyasi $penjualan): void
    {
        $nominal = (int) ($penjualan->total ?? 0);

        if ($nominal <= 0) {
            return;
        }

        $referensi = 'Jurnal Penjualan Non Konsinyasi ' . $penjualan->no_invoice;

        $jurnalLama = JurnalUmum::where('keterangan', $referensi)->first();

        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        $akunDebit     = self::getAkunDebitPenjualan($penjualan);
        $akunPenjualan = Coa::where('kode_akun', '401')->firstOrFail();

        $jurnal = JurnalUmum::create([
            'tanggal'    => $penjualan->tanggal,
            'keterangan' => $referensi,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunDebit->id,
            'debit'          => $nominal,
            'kredit'         => 0,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunPenjualan->id,
            'debit'          => 0,
            'kredit'         => $nominal,
        ]);
    }

    /**
     * Tentukan akun debit untuk jurnal penjualan non-konsinyasi.
     * - Marketplace  → Toko Online (113)
     * - Kredit       → Piutang (130)
     * - Lainnya      → Bank (112)
     */
    protected static function getAkunDebitPenjualan(PenjualanNonKonsinyasi $penjualan): Coa
    {
        if ($penjualan->jenis_penjualan === 'MARKETPLACE') {
            return Coa::where('kode_akun', '113')->firstOrFail(); // Akun Toko Online
        }

        if (($penjualan->jenis_pembayaran ?? null) === 'kredit') {
            return Coa::where('kode_akun', '130')->firstOrFail(); // Piutang
        }

        // tunai / transfer / lainnya → Bank
        return Coa::where('kode_akun', '112')->firstOrFail(); // Bank
    }

    /**
     * Jurnal saat pembayaran piutang non-konsinyasi (kredit) diterima.
     * Tunai tidak perlu jurnal pembayaran (sudah dicatat di jurnal penjualan).
     * Kredit cicilan  : Bank (D) / Piutang (K)
     * Kredit lunas    : Bank (D) / Penjualan (K)
     */
    public static function pembayaranNonKonsinyasi(Pembayaran $pembayaran): void
    {
        $nominal   = (int) ($pembayaran->jumlah_bayar ?? 0);
        $penjualan = $pembayaran->penjualan;

        if ($nominal <= 0) {
            return;
        }

        // Penjualan tunai: jurnal bank sudah dicatat di jurnal penjualan — skip.
        if ($penjualan && $penjualan->jenis_pembayaran === 'tunai') {
            return;
        }

        $referensi = 'Jurnal Pembayaran Piutang ' . $pembayaran->kode_pembayaran;

        $jurnalLama = JurnalUmum::where('keterangan', $referensi)->first();

        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        $akunDebit      = self::getAkunDebitPembayaran($pembayaran);
        $totalPenjualan = (int) ($penjualan->total ?? 0);

        // Bayar lunas sekaligus → Bank (D) / Penjualan (K)
        // Bayar cicilan         → Bank (D) / Piutang (K)
        if ($nominal >= $totalPenjualan) {
            $akunKredit = Coa::where('kode_akun', '401')->firstOrFail(); // Penjualan
        } else {
            $akunKredit = Coa::where('kode_akun', '130')->firstOrFail(); // Piutang
        }

        $jurnal = JurnalUmum::create([
            'tanggal'    => $pembayaran->tanggal_bayar ?? now(),
            'keterangan' => $referensi,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunDebit->id,
            'debit'          => $nominal,
            'kredit'         => 0,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunKredit->id,
            'debit'          => 0,
            'kredit'         => $nominal,
        ]);
    }

    /**
     * Tentukan akun debit untuk pembayaran non-konsinyasi.
     * Semua metode pembayaran masuk ke Bank (112).
     */
    protected static function getAkunDebitPembayaran(Pembayaran $pembayaran): Coa
    {
        return Coa::where('kode_akun', '112')->firstOrFail(); // Bank
    }

    // ─────────────────────────────────────────────
    // KONSINYASI
    // ─────────────────────────────────────────────

    /**
     * Jurnal saat Laporan Konsinyasi dibuat/diupdate.
     * Piutang Konsinyasi (D) / Pendapatan Konsinyasi (K)
     * Nominal: total_laporan
     */
    public static function laporanKonsinyasi(LaporanKonsinyasi $laporan): void
    {
        self::laporanKonsinyasiDenganNominal($laporan, (int) ($laporan->total_laporan ?? 0));
    }

    /**
     * Versi dengan nominal eksplisit — dipakai dari refreshTotalLaporan()
     * agar tidak bergantung pada state model yang mungkin belum terupdate.
     */
    public static function laporanKonsinyasiDenganNominal(LaporanKonsinyasi $laporan, int $nominal): void
    {
        if ($nominal <= 0) {
            return;
        }

        $referensi = 'Jurnal Laporan Konsinyasi ' . $laporan->no_laporan;

        $jurnalLama = JurnalUmum::where('keterangan', $referensi)->first();

        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        $akunPiutang    = Coa::where('kode_akun', '130')->firstOrFail(); // Piutang
        $akunPendapatan = Coa::where('kode_akun', '401')->firstOrFail(); // Pendapatan

        $jurnal = JurnalUmum::create([
            'tanggal'    => $laporan->created_at ?? now(),
            'keterangan' => $referensi,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunPiutang->id,
            'debit'          => $nominal,
            'kredit'         => 0,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunPendapatan->id,
            'debit'          => 0,
            'kredit'         => $nominal,
        ]);
    }

    /**
     * Jurnal saat pembayaran tagihan konsinyasi diterima.
     * Bank (D) / Piutang Konsinyasi (K)
     * Berlaku untuk cicilan maupun lunas — tidak dibedakan.
     */
    public static function pembayaranTagihanKonsinyasi(PembayaranTagihanKonsinyasi $pembayaran): void
    {
        $nominal = (int) ($pembayaran->jumlah_bayar ?? 0);

        if ($nominal <= 0) {
            return;
        }

        $noTagihan = $pembayaran->tagihanKonsinyasi?->no_tagihan ?? $pembayaran->tagihan_konsinyasi_id;
        $referensi = 'Jurnal Pembayaran Konsinyasi ' . $noTagihan . '-' . $pembayaran->id;

        $jurnalLama = JurnalUmum::where('keterangan', $referensi)->first();

        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        $akunBank    = self::getAkunBankPembayaranKonsinyasi($pembayaran);
        $akunPiutang = Coa::where('kode_akun', '130')->firstOrFail(); // Piutang

        $jurnal = JurnalUmum::create([
            'tanggal'    => $pembayaran->tanggal_bayar ?? now(),
            'keterangan' => $referensi,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunBank->id,
            'debit'          => $nominal,
            'kredit'         => 0,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunPiutang->id,
            'debit'          => 0,
            'kredit'         => $nominal,
        ]);
    }

    /**
     * Tentukan akun debit untuk pembayaran tagihan konsinyasi.
     * Semua metode pembayaran masuk ke Bank (112).
     */
    protected static function getAkunBankPembayaranKonsinyasi(PembayaranTagihanKonsinyasi $pembayaran): Coa
    {
        return Coa::where('kode_akun', '112')->firstOrFail(); // Bank
    }
}
