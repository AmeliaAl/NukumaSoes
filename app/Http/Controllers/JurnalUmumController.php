<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Overhead;
use App\Models\SaldoAwal;
use App\Models\Coa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
        $jurnals      = [];

        // ─── JURNAL PEMBELIAN ─────────────────────────────────────────────────

        $pembelians = Pembelian::query();

        if ($periodeAwal && $periodeAkhir) {
            $pembelians->whereBetween('tanggal', [$periodeAwal, $periodeAkhir]);
        }

        $coa552 = Coa::where('kode_akun', '552')->first();
        $coa553 = Coa::where('kode_akun', '553')->first();
        $coa554 = Coa::where('kode_akun', '554')->first();

        foreach ($pembelians->get() as $pembelian) {
            $noBukti   = 'PB-' . str_pad($pembelian->id, 3, '0', STR_PAD_LEFT);
            $subtotal  = $pembelian->subtotal ?: ($pembelian->qty * $pembelian->harga);
            $diskon    = $pembelian->diskon ?? 0;
            $ongkir    = $pembelian->ongkir ?? 0;
            $totalBersih = $pembelian->total_bersih ?? ($subtotal - $diskon);
            $grandTotal  = $pembelian->grand_total ?? ($totalBersih + $ongkir);

            $jurnals[] = [
                'tanggal'    => $pembelian->tanggal,
                'no_bukti'   => $noBukti,
                'keterangan' => 'Pembelian ' . $pembelian->bahanBaku->nama_bahan,
                'kode_akun'  => $coa552->kode_akun ?? '552',
                'nama_akun'  => $coa552->nama_akun ?? 'Pembelian Bahan Baku',
                'debit'      => $subtotal,
                'kredit'     => 0,
            ];

            if ($ongkir > 0) {
                $jurnals[] = [
                    'tanggal'    => $pembelian->tanggal,
                    'no_bukti'   => $noBukti,
                    'keterangan' => 'Ongkos Angkut Pembelian',
                    'kode_akun'  => $coa553->kode_akun ?? '553',
                    'nama_akun'  => $coa553->nama_akun ?? 'Ongkos Angkut Bahan Baku',
                    'debit'      => $ongkir,
                    'kredit'     => 0,
                ];
            }

            if ($diskon > 0) {
                $jurnals[] = [
                    'tanggal'    => $pembelian->tanggal,
                    'no_bukti'   => $noBukti,
                    'keterangan' => 'Potongan Pembelian',
                    'kode_akun'  => $coa554->kode_akun ?? '554',
                    'nama_akun'  => $coa554->nama_akun ?? 'Potongan Pembelian Bahan Baku',
                    'debit'      => 0,
                    'kredit'     => $diskon,
                ];
            }

            if (str_contains(strtolower($pembelian->coa->nama_akun), 'kas')) {
                $keteranganKredit = 'Keluar Kas Kecil';
            } elseif (str_contains(strtolower($pembelian->coa->nama_akun), 'bank')) {
                $keteranganKredit = 'Pembayaran Bank';
            } else {
                $keteranganKredit = 'Hutang Pembelian';
            }

            $jurnals[] = [
                'tanggal'    => $pembelian->tanggal,
                'no_bukti'   => $noBukti,
                'keterangan' => $keteranganKredit,
                'kode_akun'  => $pembelian->coa->kode_akun,
                'nama_akun'  => $pembelian->coa->nama_akun,
                'debit'      => 0,
                'kredit'     => $grandTotal,
            ];
        }

        // ─── JURNAL OVERHEAD ──────────────────────────────────────────────────

        $overheads = Overhead::with(['details', 'details.coa', 'details.paymentCoa']);

        if ($periodeAwal && $periodeAkhir) {
            $overheads->whereBetween('tanggal', [$periodeAwal, $periodeAkhir]);
        }

        foreach ($overheads->get() as $overhead) {
            $noBukti = 'OH-' . str_pad($overhead->id, 3, '0', STR_PAD_LEFT);

            if ($overhead->details->count() > 0) {
                foreach ($overhead->details as $detail) {
                    $jurnals[] = [
                        'tanggal'    => $overhead->tanggal,
                        'no_bukti'   => $noBukti,
                        'keterangan' => $detail->keterangan,
                        'kode_akun'  => $detail->coa->kode_akun ?? '',
                        'nama_akun'  => $detail->coa->nama_akun ?? '',
                        'debit'      => $detail->nominal,
                        'kredit'     => 0,
                    ];

                    $paymentCoa = $detail->paymentCoa;

                    $jurnals[] = [
                        'tanggal'    => $overhead->tanggal,
                        'no_bukti'   => $noBukti,
                        'keterangan' => 'Pembayaran Overhead',
                        'kode_akun'  => $paymentCoa->kode_akun ?? '111',
                        'nama_akun'  => $paymentCoa->nama_akun ?? 'Kas Kecil',
                        'debit'      => 0,
                        'kredit'     => $detail->nominal,
                    ];
                }
            } else {
                $jurnals[] = [
                    'tanggal'    => $overhead->tanggal,
                    'no_bukti'   => $noBukti,
                    'keterangan' => $overhead->keterangan,
                    'kode_akun'  => $overhead->coa->kode_akun ?? '',
                    'nama_akun'  => $overhead->coa->nama_akun ?? '',
                    'debit'      => $overhead->nominal,
                    'kredit'     => 0,
                ];

                $paymentCoa = $overhead->paymentCoa;

                $jurnals[] = [
                    'tanggal'    => $overhead->tanggal,
                    'no_bukti'   => $noBukti,
                    'keterangan' => 'Pembayaran Overhead',
                    'kode_akun'  => $paymentCoa->kode_akun ?? '111',
                    'nama_akun'  => $paymentCoa->nama_akun ?? 'Kas Kecil',
                    'debit'      => 0,
                    'kredit'     => $overhead->nominal,
                ];
            }
        }

        // ─── JURNAL SALDO AWAL ────────────────────────────────────────────────

        $coa311    = Coa::where('kode_akun', '311')->first();
        $saldoQuery = SaldoAwal::with('coa');

        if ($periodeAwal && $periodeAkhir) {
            $saldoQuery->whereBetween('tanggal', [$periodeAwal, $periodeAkhir]);
        }

        foreach ($saldoQuery->get() as $saldoAwal) {
            // Debit: akun kas/bank yang dipilih
            $jurnals[] = [
                'tanggal'    => $saldoAwal->tanggal,
                'no_bukti'   => $saldoAwal->no_bukti,
                'keterangan' => $saldoAwal->keterangan ?: ('Saldo Awal ' . ($saldoAwal->coa->nama_akun ?? '')),
                'kode_akun'  => $saldoAwal->coa->kode_akun ?? '',
                'nama_akun'  => $saldoAwal->coa->nama_akun ?? '',
                'debit'      => $saldoAwal->nominal,
                'kredit'     => 0,
            ];

            // Kredit: Modal Pemilik (311)
            $jurnals[] = [
                'tanggal'    => $saldoAwal->tanggal,
                'no_bukti'   => $saldoAwal->no_bukti,
                'keterangan' => 'Modal Pemilik',
                'kode_akun'  => $coa311->kode_akun ?? '311',
                'nama_akun'  => $coa311->nama_akun ?? 'Modal Pemilik',
                'debit'      => 0,
                'kredit'     => $saldoAwal->nominal,
            ];
        }

        // ─── SORT ─────────────────────────────────────────────────────────────

        usort($jurnals, function ($a, $b) {
            $byTanggal = strtotime($a['tanggal']) - strtotime($b['tanggal']);
            if ($byTanggal !== 0) {
                return $byTanggal;
            }
            return strcmp($a['no_bukti'], $b['no_bukti']);
        });

        return [
            'jurnals'     => $jurnals,
            'totalDebit'  => collect($jurnals)->sum('debit'),
            'totalKredit' => collect($jurnals)->sum('kredit'),
        ];
    }
}
