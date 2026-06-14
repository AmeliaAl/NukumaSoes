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
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
        ]);

        $query = Overhead::query();

        if ($request->tanggal_awal && $request->tanggal_akhir) {

            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        $overheads = $query->get();

        $total = $overheads->sum('nominal');

        return view('laporan_overhead.index', compact(
            'overheads',
            'total'
        ));
    }

    public function print(Request $request)
{
    $request->validate([
        'tanggal_awal' => 'nullable|date',
        'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
    ]);

    $query = Overhead::query();

    $tanggalAwal = $request->tanggal_awal;

    $tanggalAkhir = $request->tanggal_akhir;

    if ($tanggalAwal && $tanggalAkhir) {

        $query->whereBetween('tanggal', [
            $tanggalAwal,
            $tanggalAkhir
        ]);
    }

    $overheads = $query->get();

    $total = $overheads->sum('nominal');

    $pdf = Pdf::loadView(
        'laporan_overhead.print',
        compact(
            'overheads',
            'total',
            'tanggalAwal',
            'tanggalAkhir'
        )
    );

    return $pdf->download('laporan-overhead.pdf');
}

}