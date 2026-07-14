<?php

namespace App\Http\Controllers;

use App\Models\Overhead;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanOverheadController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_awal'  => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'jenis_periode' => 'nullable|in:harian,mingguan,bulanan',
        ]);

        $overheads = $this->buildQuery($request)->get();

        $total = $overheads->sum(fn($o) => $o->details->sum('nominal'));

        return view('laporan_overhead.index', compact('overheads', 'total'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'tanggal_awal'  => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'jenis_periode' => 'nullable|in:harian,mingguan,bulanan',
        ]);

        $overheads   = $this->buildQuery($request)->get();
        $total       = $overheads->sum(fn($o) => $o->details->sum('nominal'));
        $tanggalAwal  = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;
        $jenisPeriode = $request->jenis_periode;

        $pdf = Pdf::loadView(
            'laporan_overhead.print',
            compact('overheads', 'total', 'tanggalAwal', 'tanggalAkhir', 'jenisPeriode')
        );

        return $pdf->download('laporan-overhead.pdf');
    }

    private function buildQuery(Request $request)
    {
        $query = Overhead::with(['details.coa'])->orderBy('tanggal');

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        if ($request->jenis_periode) {
            $query->where('jenis_periode', $request->jenis_periode);
        }

        return $query;
    }

    /** Helper: format teks periode sesuai jenis */
    public static function formatPeriode(Overhead $oh): string
    {
        $jenis  = $oh->jenis_periode ?? 'harian';
        $mulai  = $oh->periode_mulai ?? $oh->tanggal;
        $akhir  = $oh->periode_akhir;

        if ($jenis === 'harian') {
            return $mulai ? \Carbon\Carbon::parse($mulai)->format('d/m/Y') : '-';
        }
        if ($jenis === 'bulanan') {
            return $mulai ? \Carbon\Carbon::parse($mulai)->translatedFormat('F Y') : '-';
        }
        // mingguan
        $txt = $mulai ? \Carbon\Carbon::parse($mulai)->format('d/m/Y') : '-';
        if ($akhir) {
            $txt .= ' – ' . \Carbon\Carbon::parse($akhir)->format('d/m/Y');
        }
        return $txt;
    }
}
