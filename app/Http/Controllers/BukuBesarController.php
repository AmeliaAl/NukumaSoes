<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\SaldoAwal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * BukuBesarController — Logika akuntansi standar
 *
 * SALDO AWAL per periode:
 *   = saldo_awals (pembuka perusahaan) + Σ mutasi jurnal_detail sebelum periode
 *   Otomatis: periode pertama → dari saldo_awals saja
 *             periode berikutnya → saldo akhir periode sebelumnya
 *
 * DOUBLE COUNTING dicegah dengan:
 *   - Jurnal SA-* hanya punya entri kredit 311, BUKAN debit Kas/Bank
 *   - Saldo Kas/Bank diambil dari saldo_awals sebagai saldo pembuka
 *
 * SATU LOGIKA untuk semua akun — tidak ada hardcode per akun
 */
class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'periode_awal'  => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
            'akun_id'       => 'nullable|string',
        ]);

        $data = $this->buildBukuBesar($request);

        return view('buku_besar.index', $data);
    }

    public function print(Request $request)
    {
        $request->validate([
            'periode_awal'  => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
            'akun_id'       => 'nullable|string',
        ]);

        $data = $this->buildBukuBesar($request);

        $pdf = Pdf::loadView('buku_besar.print', $data);

        return $pdf->download('buku-besar.pdf');
    }

    private function buildBukuBesar(Request $request)
    {
        $coas         = Akun::orderBy('no_akun')->get();
        $akunId       = (string) ($request->akun_id ?? '');
        $periodeAwal  = $request->periode_awal;
        $periodeAkhir = $request->periode_akhir;

        // ── Saldo Awal ────────────────────────────────────────────────────
        // Saldo Awal = saldo pembuka (saldo_awals) + semua mutasi sebelum periode
        // Berlaku untuk semua akun — tidak ada logika khusus per akun
        $saldoAwalNominal = 0.0;

        if ($akunId !== '' && $periodeAwal) {
            $saldoAwalNominal = $this->hitungSaldoSebelumPeriode($akunId, $periodeAwal);
        }

        // ── Ambil transaksi periode yang dipilih ──────────────────────────
        $jurnals = [];

        if ($akunId !== '' && $periodeAwal && $periodeAkhir) {
            $details = JurnalDetail::with('jurnal')
                ->where('no_akun', $akunId)
                ->whereHas('jurnal', fn($q) =>
                    $q->whereBetween('tanggal', [$periodeAwal, $periodeAkhir])
                )
                ->join('jurnal', 'jurnal.id', '=', 'jurnal_detail.id_jurnal')
                ->orderBy('jurnal.tanggal')
                ->orderBy('jurnal.id')
                ->orderBy('jurnal_detail.id')
                ->select('jurnal_detail.*')
                ->get();

            foreach ($details as $d) {
                $tgl = $d->jurnal->tanggal instanceof \Carbon\Carbon
                    ? $d->jurnal->tanggal->format('Y-m-d')
                    : (string) $d->jurnal->tanggal;

                // Keterangan: pakai deskripsi detail, fallback ke deskripsi jurnal
                // Singkat dan deskriptif — tidak pakai kalimat panjang
                $keterangan = $d->deskripsi ?: $d->jurnal->deskripsi ?: $d->jurnal->no_referensi;

                $jurnals[] = [
                    'tanggal'    => $tgl,
                    'bukti'      => $d->jurnal->no_referensi,
                    'keterangan' => $keterangan,
                    'ref'        => $d->no_akun,
                    'debit'      => (float) $d->debit,
                    'kredit'     => (float) $d->credit,
                    'saldo'      => 0,
                ];
            }
        }

        // ── Hitung saldo berjalan ─────────────────────────────────────────
        $saldo = $saldoAwalNominal;
        foreach ($jurnals as $key => $jurnal) {
            $saldo += $jurnal['debit'];
            $saldo -= $jurnal['kredit'];
            $jurnals[$key]['saldo'] = $saldo;
        }

        $saldoAkhir  = $saldo;
        $totalDebit  = collect($jurnals)->sum('debit');
        $totalKredit = collect($jurnals)->sum('kredit');
        $akun        = Akun::where('no_akun', $akunId)->first();

        return [
            'coas'             => $coas,
            'akun'             => $akun,
            'jurnals'          => $jurnals,
            'saldoAwalNominal' => $saldoAwalNominal,
            'periodeAwal'      => $periodeAwal,
            'periodeAkhir'     => $periodeAkhir,
            'totalDebit'       => $totalDebit,
            'totalKredit'      => $totalKredit,
            'saldoAkhir'       => $saldoAkhir,
        ];
    }

    /**
     * Hitung saldo kumulatif akun SEBELUM tanggal periode_awal.
     *
     * Formula:
     *   saldo = saldo_awals[akun]
     *         + Σ(debit - kredit) dari jurnal_detail WHERE tanggal < periode_awal
     *           KECUALI entri debit dari jurnal SA-* (setoran modal)
     *           karena sudah masuk di saldo_awals
     *
     * Mengapa kecualikan debit SA-*?
     * - Jurnal Umum membutuhkan debit Kas di SA-* agar balance
     * - Buku Besar Kas tidak boleh menghitung debit SA-* karena sudah di saldo_awals
     * - Dengan filter ini, tidak ada double counting di Buku Besar
     */
    private function hitungSaldoSebelumPeriode(string $noAkun, string $periodeAwal): float
    {
        // Saldo pembuka dari saldo_awals
        $saldoPembuka = (float) SaldoAwal::whereHas('coa', fn($q) =>
            $q->where('no_akun', $noAkun)
        )->sum('nominal');

        // No-referensi dari jurnal Setoran Modal (SA-*)
        $saReferensi = \App\Models\Jurnal::where('no_referensi', 'like', 'SA-%')
            ->pluck('id')
            ->toArray();

        // Mutasi sebelum periode, KECUALI debit dari jurnal SA-* untuk akun ini
        $query = JurnalDetail::where('no_akun', $noAkun)
            ->whereHas('jurnal', fn($q) =>
                $q->where('tanggal', '<', $periodeAwal)
            );

        // Untuk akun yang ada di saldo_awals (Kas/Bank): kecualikan debit SA-*
        if ($saldoPembuka > 0 && !empty($saReferensi)) {
            $query->where(function($q) use ($saReferensi) {
                $q->whereNotIn('id_jurnal', $saReferensi)
                  ->orWhere('credit', '>', 0); // tetap hitung kredit SA-* jika ada
            });
        }

        $mutasi = $query->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        return $saldoPembuka + (float)$mutasi->total_debit - (float)$mutasi->total_credit;
    }
}
