<?php

namespace App\Traits;

use App\Models\Akun;
use App\Models\Pembelian;
use App\Models\Overhead;
use App\Models\SaldoAwal;

/**
 * Trait SaldoAwalCalculator
 *
 * ══════════════════════════════════════════════════════════════
 * KONSEP AKUNTANSI
 * ══════════════════════════════════════════════════════════════
 *
 * A. Akun Kas / Bank (normal saldo Debit)
 *    ─────────────────────────────────────
 *    Saldo Pembuka  = diambil dari tabel saldo_awals untuk akun yang dipilih.
 *    Saldo Berjalan = Saldo Pembuka + mutasi transaksi periode ini.
 *
 * B. Akun Modal Pemilik / 311 (normal saldo Kredit)
 *    ─────────────────────────────────────────────
 *    Saldo Pembuka  = 0  (TIDAK dari tabel saldo_awals)
 *    Saldo Modal terbentuk dari jurnal pembukaan yang otomatis dibuat
 *    saat user input saldo awal Kas/Bank:
 *      Debit  → Kas / Bank  (dari saldo_awals)
 *      Kredit → Modal Pemilik (311) ← inilah yang tampil di Buku Besar 311
 *
 * C. Akun lain (beban, pendapatan, dsb)
 *    ─────────────────────────────────
 *    Saldo Pembuka  = 0 (tidak ada di saldo_awals)
 *    Saldo Berjalan = akumulasi mutasi transaksi saja.
 *
 * ══════════════════════════════════════════════════════════════
 * CARA MENAMBAH MODUL BARU
 * ══════════════════════════════════════════════════════════════
 * 1. Buat method private mutasiNamaModul(string $akunId, string $sebelumTanggal): float
 * 2. Daftarkan di getMutasiProviders():
 *       'nama_modul' => fn($a, $t) => $this->mutasiNamaModul($a, $t),
 * Tidak perlu mengubah hitungSaldoAwal() sama sekali.
 */
trait SaldoAwalCalculator
{
    /**
     * Hitung saldo awal sebuah akun untuk periode tertentu.
     *
     * @param string $akunId       kode_akun yang dipilih di filter
     * @param string $periodeAwal  tanggal awal periode (Y-m-d)
     * @return float
     */
    protected function hitungSaldoAwal(string $akunId, string $periodeAwal): float
    {
        // Step 1 — Saldo pembuka akun ini (akun-spesifik)
        $saldoPembuka = $this->getSaldoPembuka($akunId);

        // Step 2 — Akumulasi mutasi dari SEMUA modul sebelum periode
        $mutasiSebelum = 0.0;
        foreach ($this->getMutasiProviders() as $provider) {
            $mutasiSebelum += $provider($akunId, $periodeAwal);
        }

        // Step 3 — Saldo Awal Periode = Pembuka + Mutasi Sebelum
        return $saldoPembuka + $mutasiSebelum;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // STEP 1 IMPLEMENTATION
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Ambil saldo pembuka untuk akun yang dipilih.
     *
     * - Kas/Bank        → ambil dari saldo_awals WHERE coa.kode_akun = $akunId
     * - Modal Pemilik   → 0 (saldo terbentuk dari jurnal, bukan dari saldo_awals)
     * - Akun lain       → 0
     *
     * @param string $akunId
     * @return float
     */
    private function getSaldoPembuka(string $akunId): float
    {
        $coa311 = Akun::where('no_akun', '311')->orWhere('kode_akun', '311')->first();

        // Modal Pemilik (311) — saldo pembuka 0, karena saldo terbentuk dari jurnal
        if ($coa311 && $akunId === $coa311->no_akun) {
            return 0.0;
        }

        // Cari saldo awal yang cocok dengan akun yang dipilih (akun-spesifik)
        $record = SaldoAwal::whereHas('coa', fn($q) => $q->where('no_akun', $akunId)->orWhere('kode_akun', $akunId))
                    ->first();

        return $record ? (float) $record->nominal : 0.0;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // STEP 2 IMPLEMENTATION — PROVIDER REGISTRY
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Daftar provider mutasi per modul.
     *
     * Setiap entry adalah Closure:
     *   fn(string $akunId, string $sebelumTanggal): float
     *
     * Return = net effect transaksi ke akun tersebut:
     *   +  = menambah saldo (debit ke akun normal debit)
     *   -  = mengurangi saldo (kredit ke akun normal debit)
     *
     * ── Cara menambah modul baru ──────────────────────────────────────────
     * 1. Buat: private function mutasiNamaModul(string $a, string $t): float
     * 2. Daftar di sini: 'nama_modul' => fn($a, $t) => $this->mutasiNamaModul($a, $t),
     * Tidak perlu ubah hitungSaldoAwal().
     *
     * @return array<string, \Closure>
     */
    private function getMutasiProviders(): array
    {
        return [
            // ── Modul aktif ────────────────────────────────────────────────
            'pembelian'  => fn($a, $t) => $this->mutasiPembelian($a, $t),
            'overhead'   => fn($a, $t) => $this->mutasiOverhead($a, $t),
            'setoran_modal' => fn($a, $t) => $this->mutasiSetoranModal($a, $t),

            // ── Modul mendatang — uncomment saat siap ──────────────────────
            // 'penjualan'    => fn($a, $t) => $this->mutasiPenjualan($a, $t),
            // 'produksi'     => fn($a, $t) => $this->mutasiProduksi($a, $t),
            // 'penyesuaian'  => fn($a, $t) => $this->mutasiPenyesuaian($a, $t),
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PROVIDER IMPLEMENTATIONS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Mutasi Setoran Modal Awal sebelum periode.
     *
     * Jurnal yang terbentuk saat input saldo awal:
     *   Debit  Kas/Bank      → untuk Buku Besar Kas/Bank (sudah dari getSaldoPembuka)
     *   Kredit Modal Pemilik → untuk Buku Besar Modal Pemilik (311)
     *
     * Provider ini khusus menangani efek ke akun Modal Pemilik.
     * Untuk akun Kas/Bank, saldo pembuka sudah ditangani di getSaldoPembuka().
     */
    private function mutasiSetoranModal(string $akunId, string $sebelumTanggal): float
    {
        $coa311 = Akun::where('no_akun', '311')->orWhere('kode_akun', '311')->first();
        if (!$coa311) {
            return 0.0;
        }

        // Hanya relevan untuk akun Modal Pemilik (311)
        $noAkun311 = $coa311->no_akun ?? $coa311->kode_akun ?? '311';
        if ($akunId !== $noAkun311) {
            return 0.0;
        }

        // Total seluruh setoran modal sebelum periode = kredit ke 311 (negatif)
        $total = SaldoAwal::where('tanggal', '<', $sebelumTanggal)->sum('nominal');

        return -(float) $total;
    }

    /**
     * Net mutasi modul Pembelian Bahan Baku sebelum $sebelumTanggal.
     *
     * Jurnal per transaksi:
     *   Debit  552 (Pembelian Bahan Baku) = subtotal
     *   Debit  553 (Ongkos Angkut)        = ongkir (jika ada)
     *   Kredit 554 (Potongan Pembelian)   = diskon (jika ada)
     *   Kredit Kas/Bank                   = grand_total
     */
    private function mutasiPembelian(string $akunId, string $sebelumTanggal): float
    {
        $net = 0.0;

        $pembelians = Pembelian::with('coa')
            ->where('tanggal', '<', $sebelumTanggal)
            ->get();

        foreach ($pembelians as $pb) {
            $subtotal    = (float) ($pb->subtotal ?: ($pb->qty * $pb->harga));
            $diskon      = (float) ($pb->diskon  ?? 0);
            $ongkir      = (float) ($pb->ongkir  ?? 0);
            $totalBersih = (float) ($pb->total_bersih ?? ($subtotal - $diskon));
            $grandTotal  = (float) ($pb->grand_total  ?? ($totalBersih + $ongkir));

            if ($akunId === '552') { $net += $subtotal; }
            if ($akunId === '553' && $ongkir > 0) { $net += $ongkir; }
            if ($akunId === '554' && $diskon > 0) { $net -= $diskon; }

            if ($pb->coa && $akunId === (string) $pb->coa->kode_akun) {
                $net -= $grandTotal; // Kas/Bank berkurang
            }
        }

        return $net;
    }

    /**
     * Net mutasi modul Overhead sebelum $sebelumTanggal.
     *
     * Jurnal per detail:
     *   Debit  Akun Beban Overhead = nominal
     *   Kredit Akun Pembayaran     = nominal
     */
    private function mutasiOverhead(string $akunId, string $sebelumTanggal): float
    {
        $net = 0.0;

        $overheads = Overhead::with(['details.coa', 'details.paymentCoa', 'coa', 'paymentCoa'])
            ->where('tanggal', '<', $sebelumTanggal)
            ->get();

        foreach ($overheads as $oh) {
            if ($oh->details->count() > 0) {
                foreach ($oh->details as $det) {
                    if ($akunId === (string) ($det->coa->kode_akun ?? '')) {
                        $net += $det->nominal; // Beban — debit
                    }
                    if ($det->paymentCoa && $akunId === (string) $det->paymentCoa->kode_akun) {
                        $net -= $det->nominal; // Kas/Bank — kredit
                    }
                }
            } else {
                // Legacy: tanpa detail
                if ($akunId === (string) ($oh->coa->kode_akun ?? '')) {
                    $net += $oh->nominal;
                }
                if ($oh->paymentCoa && $akunId === (string) $oh->paymentCoa->kode_akun) {
                    $net -= $oh->nominal;
                }
            }
        }

        return $net;
    }

    // ── Template modul mendatang ───────────────────────────────────────────────
    //
    // private function mutasiPenjualan(string $akunId, string $sebelumTanggal): float
    // {
    //     $net = 0.0;
    //     // Contoh: debit Kas/Bank, kredit Pendapatan
    //     // $penjualans = Penjualan::where('tanggal', '<', $sebelumTanggal)->get();
    //     // foreach ($penjualans as $pj) {
    //     //     if ($akunId === $pj->coaPembayaran->kode_akun) { $net += $pj->total; }
    //     //     if ($akunId === $pj->coaPendapatan->kode_akun) { $net -= $pj->total; }
    //     // }
    //     return $net;
    // }
}
