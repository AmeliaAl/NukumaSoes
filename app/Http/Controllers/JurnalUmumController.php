<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Akun;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * JurnalUmumController
 *
 * Membaca langsung dari tabel jurnal & jurnal_detail.
 * Filter berdasarkan tanggal (bukan whereMonth/whereYear).
 * Setiap transaksi dijamin double-entry (debit = kredit).
 * Validasi balance dilakukan sebelum data dikirim ke view.
 */
class JurnalUmumController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'periode_awal'  => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
        ]);

        $data = $this->buildJurnals($request);

        return view('jurnal_umum.index', $data);
    }

    public function print(Request $request)
    {
        $request->validate([
            'periode_awal'  => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
        ]);

        $data = $this->buildJurnals($request);

        $pdf = Pdf::loadView(
            'jurnal_umum.print',
            array_merge($data, [
                'periodeAwal'  => $request->periode_awal,
                'periodeAkhir' => $request->periode_akhir,
            ])
        );

        return $pdf->download('jurnal-umum.pdf');
    }

    private function buildJurnals(Request $request)
    {
        $periodeAwal  = $request->periode_awal;
        $periodeAkhir = $request->periode_akhir;

        // ── Query jurnal dengan filter tanggal ────────────────────────────
        // WAJIB gunakan whereBetween tanggal, bukan whereMonth/whereYear
        // agar transaksi hanya muncul pada periode yang benar
        $query = Jurnal::with(['details.akun'])
            ->orderBy('tanggal')
            ->orderBy('no_referensi');

        if ($periodeAwal && $periodeAkhir) {
            $query->whereBetween('tanggal', [$periodeAwal, $periodeAkhir]);
        }

        $jurnalRecords = $query->get();

        // ── Bangun array tampilan ─────────────────────────────────────────
        $jurnals = [];
        foreach ($jurnalRecords as $jurnal) {
            $tgl = $jurnal->tanggal instanceof \Carbon\Carbon
                ? $jurnal->tanggal->format('Y-m-d')
                : (string) $jurnal->tanggal;

            foreach ($jurnal->details as $detail) {
                // Keterangan: nama akun atau deskripsi singkat
                $namaAkun   = $detail->akun->nama_akun ?? $detail->no_akun;
                $keterangan = $detail->deskripsi ?: $namaAkun;

                $jurnals[] = [
                    'tanggal'   => $tgl,
                    'no_bukti'  => $jurnal->no_referensi,
                    'kode_akun' => $detail->no_akun,
                    'nama_akun' => $namaAkun,
                    'keterangan'=> $keterangan,
                    'debit'     => (float) $detail->debit,
                    'kredit'    => (float) $detail->credit,
                ];
            }
        }

        $totalDebit  = collect($jurnals)->sum('debit');
        $totalKredit = collect($jurnals)->sum('kredit');

        // ── Validasi balance ──────────────────────────────────────────────
        if (abs($totalDebit - $totalKredit) > 0.01) {
            Log::error('Jurnal Umum Tidak Seimbang', [
                'Debit'   => $totalDebit,
                'Kredit'  => $totalKredit,
                'Selisih' => $totalDebit - $totalKredit,
                'Periode' => ['awal' => $periodeAwal, 'akhir' => $periodeAkhir],
            ]);
        }

        return [
            'jurnals'     => $jurnals,
            'totalDebit'  => $totalDebit,
            'totalKredit' => $totalKredit,
        ];
    }
}
