<?php

namespace App\Http\Controllers;

use App\Models\BiayaOverheadPabrik;
use App\Models\BiayaTenagaKerja;
use App\Models\PemakaianBahanBaku;
use App\Models\PermintaanProduksi;
use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanBiayaProduksiController extends Controller
{
    /**
     * Display a listing of completed job orders.
     */
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'id_produk' => 'nullable|exists:produk,id_produk',
        ]);

        $query = PermintaanProduksi::with('produk')
            ->where('status', 'selesai')
            ->orderBy('tanggal_mulai', 'desc');

        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_mulai', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_mulai', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        $jobOrders = $query->paginate(15);
        $produkList = Produk::orderBy('nama_produk')->get();

        return view('laporan.biaya-produksi.index', compact('jobOrders', 'produkList'));
    }

    public function show($id)
    {
        $jobOrder = $this->findJobOrderWithCostDetails($id);

        return view('laporan.biaya-produksi.show', compact('jobOrder'));
    }

    public function kartuBiaya($id)
    {
        $jobOrder = $this->findJobOrderWithCostDetails($id);

        return view('laporan.biaya-produksi.kartu-biaya', compact('jobOrder'));
    }

    public function exportPdf($id)
    {
        $jobOrder = $this->findJobOrderWithCostDetails($id);
        $pdf = Pdf::loadView('laporan.biaya-produksi.pdf', compact('jobOrder'));

        return $pdf->download('Kartu-Biaya-' . $jobOrder->nomor_job . '.pdf');
    }

    private function findJobOrderWithCostDetails($id): PermintaanProduksi
    {
        return PermintaanProduksi::with([
            'produk',
            'admin',
            'pemakaianBahanBaku.bahanBaku',
            'pemakaianBahanBaku.stokBahanBaku',
            'biayaTenagaKerja.tenagaKerja',
            'biayaOverheadPabrik',
        ])->findOrFail($id);
    }
}
