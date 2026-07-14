<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;

/**
 * JurnalService
 *
 * Bertanggung jawab membuat satu record jurnal + detail debit/kredit
 * untuk setiap transaksi. Dipanggil dari controller setelah menyimpan
 * transaksi utama (Pembelian, Overhead, SaldoAwal).
 *
 * Cara menambah modul baru:
 *   Buat method static baru di class ini, panggil buatJurnal() di dalam.
 */
class JurnalService
{
    /**
     * Buat satu jurnal + N detail.
     *
     * @param  string $tanggal      Y-m-d
     * @param  string $noReferensi  PB-001 / OH-001 / SA-001
     * @param  string $deskripsi    Ringkasan transaksi
     * @param  array  $entries      [['no_akun'=>'111','deskripsi'=>'...','debit'=>X,'credit'=>Y], ...]
     * @return Jurnal
     */
    public static function buatJurnal(
        string $tanggal,
        string $noReferensi,
        string $deskripsi,
        array  $entries
    ): Jurnal {
        $jurnal = Jurnal::create([
            'tanggal'      => $tanggal,
            'no_referensi' => $noReferensi,
            'deskripsi'    => $deskripsi,
        ]);

        foreach ($entries as $entry) {
            $jurnal->details()->create([
                'no_akun'    => $entry['no_akun'],
                'deskripsi'  => $entry['deskripsi'] ?? null,
                'debit'      => $entry['debit']  ?? 0,
                'credit'     => $entry['credit'] ?? 0,
            ]);
        }

        return $jurnal;
    }

    // ──────────────────────────────────────────────────────────────────────
    // SETORAN MODAL AWAL
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Jurnal Setoran Modal Awal — Double Entry yang benar:
     *   Debit  → Kas/Bank (akun yang dipilih)
     *   Kredit → Modal Pemilik (311)
     *
     * CATATAN untuk Buku Besar:
     * - Buku Besar Kas/Bank: saldo awal dari saldo_awals (bukan dari jurnal ini)
     *   sehingga entri debit di sini TIDAK dihitung ulang di hitungSaldoSebelumPeriode()
     *   untuk menghindari double counting.
     * - Buku Besar 311: saldo dari kredit jurnal ini (tidak ada di saldo_awals)
     *
     * CATATAN untuk Jurnal Umum:
     * - Kedua sisi wajib ada agar jurnal balance (double-entry accounting)
     */
    public static function jurnalSetoranModal(
        string $tanggal,
        string $noBukti,
        string $noAkunKas,
        string $namaAkunKas,
        float  $nominal
    ): Jurnal {
        return self::buatJurnal(
            $tanggal,
            $noBukti,
            'Setoran Modal Awal',
            [
                ['no_akun' => $noAkunKas, 'deskripsi' => $namaAkunKas, 'debit' => $nominal, 'credit' => 0],
                ['no_akun' => '311',      'deskripsi' => 'Modal Pemilik', 'debit' => 0, 'credit' => $nominal],
            ]
        );
    }

    // ──────────────────────────────────────────────────────────────────────
    // PEMBELIAN BAHAN BAKU
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Jurnal Pembelian Bahan Baku:
     *   Debit  → 552 Pembelian Bahan Baku (subtotal)
     *   Debit  → 553 Ongkos Angkut (jika ada)
     *   Kredit → 554 Potongan Pembelian (jika ada)
     *   Kredit → Kas/Bank (grand_total)
     */
    public static function jurnalPembelian(
        string $tanggal,
        string $noPembelian,
        float  $subtotal,
        float  $diskon,
        float  $ongkir,
        float  $grandTotal,
        string $noAkunBayar,
        string $namaAkunBayar
    ): Jurnal {
        $entries = [];

        // Debit: Pembelian Bahan Baku
        $entries[] = [
            'no_akun'   => '552',
            'deskripsi' => 'Pembelian Bahan Baku',
            'debit'     => $subtotal,
            'credit'    => 0,
        ];

        // Debit: Ongkos Angkut
        if ($ongkir > 0) {
            $entries[] = [
                'no_akun'   => '553',
                'deskripsi' => 'Ongkos Angkut Pembelian',
                'debit'     => $ongkir,
                'credit'    => 0,
            ];
        }

        // Kredit: Potongan Pembelian
        if ($diskon > 0) {
            $entries[] = [
                'no_akun'   => '554',
                'deskripsi' => 'Potongan Pembelian',
                'debit'     => 0,
                'credit'    => $diskon,
            ];
        }

        // Kredit: Kas/Bank
        $entries[] = [
            'no_akun'   => $noAkunBayar,
            'deskripsi' => 'Pembayaran Pembelian Bahan Baku',
            'debit'     => 0,
            'credit'    => $grandTotal,
        ];

        return self::buatJurnal($tanggal, $noPembelian, 'Pembelian Bahan Baku', $entries);
    }

    // ──────────────────────────────────────────────────────────────────────
    // OVERHEAD
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Jurnal Overhead (per detail item):
     *   Debit  → Akun Beban Overhead
     *   Kredit → Akun Pembayaran (Kas/Bank)
     *
     * @param array $details [['no_akun_beban'=>'612','nama_beban'=>'...','no_akun_bayar'=>'111','nominal'=>X], ...]
     */
    public static function jurnalOverhead(
        string $tanggal,
        string $noOverhead,
        array  $details
    ): Jurnal {
        $entries = [];

        foreach ($details as $d) {
            // Debit: akun beban
            $entries[] = [
                'no_akun'   => $d['no_akun_beban'],
                'deskripsi' => $d['nama_beban'] ?? 'Biaya Overhead',
                'debit'     => (float) $d['nominal'],
                'credit'    => 0,
            ];

            // Kredit: akun pembayaran
            $entries[] = [
                'no_akun'   => $d['no_akun_bayar'],
                'deskripsi' => 'Pembayaran Biaya Overhead',
                'debit'     => 0,
                'credit'    => (float) $d['nominal'],
            ];
        }

        return self::buatJurnal($tanggal, $noOverhead, 'Biaya Overhead', $entries);
    }

    // ──────────────────────────────────────────────────────────────────────
    // MIGRASI DATA LAMA → JURNAL
    // Dipanggil dari migration atau seeder untuk isi tabel jurnal dari data lama.
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Isi tabel jurnal dari seluruh data transaksi yang sudah ada.
     * Idempotent: skip jika no_referensi sudah ada di tabel jurnal.
     */
    public static function migrasiDataLama(): void
    {
        // ── Saldo Awal ────────────────────────────────────────────────────
        $saldoAwals = \App\Models\SaldoAwal::with('coa')->get();
        foreach ($saldoAwals as $sa) {
            if (Jurnal::where('no_referensi', $sa->no_bukti)->exists()) continue;

            $noAkunKas = $sa->coa->no_akun ?? $sa->coa->kode_akun ?? '111';
            self::jurnalSetoranModal(
                $sa->tanggal,
                $sa->no_bukti,
                $noAkunKas,
                $sa->coa->nama_akun ?? 'Kas',
                (float) $sa->nominal
            );
        }

        // ── Pembelian ─────────────────────────────────────────────────────
        $pembelians = \App\Models\Pembelian::with('coa')->get();
        foreach ($pembelians as $pb) {
            $noRef = 'PB-' . str_pad($pb->id, 3, '0', STR_PAD_LEFT);
            if (Jurnal::where('no_referensi', $noRef)->exists()) continue;

            $subtotal    = (float) ($pb->subtotal ?: ($pb->qty * $pb->harga));
            $diskon      = (float) ($pb->diskon ?? 0);
            $ongkir      = (float) ($pb->ongkir ?? 0);
            $totalBersih = (float) ($pb->total_bersih ?? ($subtotal - $diskon));
            $grandTotal  = (float) ($pb->grand_total  ?? ($totalBersih + $ongkir));

            $noAkunBayar  = $pb->coa->no_akun ?? $pb->coa->kode_akun ?? '111';
            $namaAkunBayar = $pb->coa->nama_akun ?? 'Kas';

            self::jurnalPembelian(
                $pb->tanggal, $noRef,
                $subtotal, $diskon, $ongkir, $grandTotal,
                $noAkunBayar, $namaAkunBayar
            );
        }

        // ── Overhead ──────────────────────────────────────────────────────
        $overheads = \App\Models\Overhead::with(['details.coa', 'details.paymentCoa', 'coa', 'paymentCoa'])->get();
        foreach ($overheads as $oh) {
            $noRef = 'OH-' . str_pad($oh->id, 3, '0', STR_PAD_LEFT);
            if (Jurnal::where('no_referensi', $noRef)->exists()) continue;

            $details = [];

            if ($oh->details->count() > 0) {
                foreach ($oh->details as $det) {
                    $details[] = [
                        'no_akun_beban' => $det->coa->no_akun ?? $det->coa->kode_akun ?? '',
                        'nama_beban'    => $det->coa->nama_akun ?? 'Biaya Overhead',
                        'no_akun_bayar' => $det->paymentCoa->no_akun ?? $det->paymentCoa->kode_akun ?? '111',
                        'nominal'       => $det->nominal,
                    ];
                }
            } else {
                $details[] = [
                    'no_akun_beban' => $oh->coa->no_akun ?? $oh->coa->kode_akun ?? '',
                    'nama_beban'    => $oh->coa->nama_akun ?? 'Biaya Overhead',
                    'no_akun_bayar' => $oh->paymentCoa->no_akun ?? $oh->paymentCoa->kode_akun ?? '111',
                    'nominal'       => $oh->nominal,
                ];
            }

            self::jurnalOverhead($oh->tanggal, $noRef, $details);
        }
    }
}
