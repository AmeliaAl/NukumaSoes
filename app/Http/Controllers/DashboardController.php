<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\BatchProduksi;
use App\Models\PermintaanProduksi;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard
     */
    public function index()
    {
        $jobPending = PermintaanProduksi::where('status', 'pending')->count();
        $jobProses = PermintaanProduksi::where('status', 'proses')->count();
        $jobSelesai = PermintaanProduksi::where('status', 'selesai')->count();
        $jobAktif = $jobPending + $jobProses;

        $batchHariIni = BatchProduksi::whereDate('tanggal_mulai', today())
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->count();

        $stokMenipis = BahanBaku::where('status', 'aktif')
            ->whereColumn('stok_saat_ini', '<', 'stok_minimum')
            ->count();

        $jobTerbaru = PermintaanProduksi::with(['produk', 'admin'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $totalBiayaBulanIni = PermintaanProduksi::whereMonth('tanggal_mulai', now()->month)
            ->whereYear('tanggal_mulai', now()->year)
            ->sum('total_biaya_produksi');

        return view('dashboard', compact(
            'jobAktif',
            'jobPending',
            'jobProses',
            'jobSelesai',
            'batchHariIni',
            'stokMenipis',
            'jobTerbaru',
            'totalBiayaBulanIni'
        ));
    }

    /**
     * Get alert untuk stok menipis
     */
    public function getStokAlert()
    {
        $stokMenipis = BahanBaku::where('status', 'aktif')
                                ->whereColumn('stok_saat_ini', '<', 'stok_minimum')
                                ->select('nama_bahan', 'stok_saat_ini', 'stok_minimum', 'satuan')
                                ->get();

        return response()->json([
            'count' => $stokMenipis->count(),
            'data' => $stokMenipis
        ]);
    }
}
