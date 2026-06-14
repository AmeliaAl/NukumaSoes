<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPembelianController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
        ]);

        $pembelians = $this->buildQuery($request)->get();

        $summary = $this->makeSummary($pembelians);

        return view('laporan_pembelian.index', compact(
            'pembelians',
            'summary'
        ));
    }

    public function print(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
        ]);

        $pembelians = $this->buildQuery($request)->get();

        $summary = $this->makeSummary($pembelians);

        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $pdf = Pdf::loadView(
            'laporan_pembelian.print',
            compact(
                'pembelians',
                'summary',
                'tanggalAwal',
                'tanggalAkhir'
            )
        );

        return $pdf->download('laporan-pembelian.pdf');
    }

    private function buildQuery(Request $request)
    {
        $query = Pembelian::with(['supplier', 'details.bahanBaku', 'coa'])->orderBy('tanggal')->orderBy('no_pembelian');

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        return $query;
    }

    private function makeSummary($pembelians)
    {
        return [
            'subtotal' => $pembelians->sum(function ($pembelian) {
                return $pembelian->subtotal ?: ($pembelian->qty * $pembelian->harga);
            }),
            'diskon' => $pembelians->sum(function ($pembelian) {
                return $pembelian->diskon ?? 0;
            }),
            'total_bersih' => $pembelians->sum(function ($pembelian) {
                $subtotal = $pembelian->subtotal ?: ($pembelian->qty * $pembelian->harga);
                $diskon = $pembelian->diskon ?? 0;

                return $pembelian->total_bersih ?? ($subtotal - $diskon);
            }),
            'ongkir' => $pembelians->sum(function ($pembelian) {
                return $pembelian->ongkir ?? 0;
            }),
            'grand_total' => $pembelians->sum(function ($pembelian) {
                $subtotal = $pembelian->subtotal ?: ($pembelian->qty * $pembelian->harga);
                $diskon = $pembelian->diskon ?? 0;
                $totalBersih = $pembelian->total_bersih ?? ($subtotal - $diskon);
                $ongkir = $pembelian->ongkir ?? 0;

                return $pembelian->grand_total ?? ($totalBersih + $ongkir);
            }),
        ];
    }
}
