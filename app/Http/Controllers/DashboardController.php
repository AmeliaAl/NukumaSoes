<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\TenagaKerja;
use App\Models\Produk;
use App\Models\PermintaanProduksi;
use App\Models\PermintaanBahanBaku;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard
     */
    public function index()
    {
        // Card Statistics
        $totalBahanBaku = BahanBaku::where('status', 'aktif')->count();
        $totalTenagaKerja = TenagaKerja::where('status', 'aktif')->count();
        $totalProduk = Produk::where('status', 'aktif')->count();
        
        // Job Order Statistics
        $jobPending = PermintaanProduksi::where('status', 'pending')->count();
        $jobProses = PermintaanProduksi::where('status', 'proses')->count();
        $jobSelesai = PermintaanProduksi::where('status', 'selesai')->count();
        $totalJob = $jobPending + $jobProses + $jobSelesai;

        // Stok Menipis (stok < stok minimum)
        $stokMenipis = BahanBaku::where('status', 'aktif')
                                ->whereColumn('stok_saat_ini', '<', 'stok_minimum')
                                ->get();

        // Permintaan Bahan Baku Pending
        $permintaanPending = PermintaanBahanBaku::with(['bahanBaku', 'admin'])
                                                ->where('status_permintaan', 'pending')
                                                ->orderBy('tanggal_permintaan', 'desc')
                                                ->limit(5)
                                                ->get();

        // Job Order Terbaru
        $jobTerbaru = PermintaanProduksi::with(['produk', 'admin'])
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();

        // Total Biaya Produksi Bulan Ini
        $totalBiayaBulanIni = PermintaanProduksi::whereMonth('tanggal_mulai', now()->month)
                                                ->whereYear('tanggal_mulai', now()->year)
                                                ->sum('total_biaya_produksi');

        // Chart Data: Job Order per Bulan (6 bulan terakhir)
        $jobPerBulan = PermintaanProduksi::select(
                            DB::raw('MONTH(tanggal_mulai) as bulan'),
                            DB::raw('YEAR(tanggal_mulai) as tahun'),
                            DB::raw('COUNT(*) as total')
                        )
                        ->where('tanggal_mulai', '>=', now()->subMonths(6))
                        ->groupBy('tahun', 'bulan')
                        ->orderBy('tahun', 'asc')
                        ->orderBy('bulan', 'asc')
                        ->get();

        // Chart Data: Top 5 Produk Terlaris (berdasarkan jumlah produksi)
        $topProduk = PermintaanProduksi::select(
                            'id_produk',
                            DB::raw('SUM(jumlah_produksi) as total_produksi')
                        )
                        ->with('produk')
                        ->where('status', 'selesai')
                        ->groupBy('id_produk')
                        ->orderBy('total_produksi', 'desc')
                        ->limit(5)
                        ->get();

        return view('dashboard', compact(
            'totalBahanBaku',
            'totalTenagaKerja',
            'totalProduk',
            'totalJob',
            'jobPending',
            'jobProses',
            'jobSelesai',
            'stokMenipis',
            'permintaanPending',
            'jobTerbaru',
            'totalBiayaBulanIni',
            'jobPerBulan',
            'topProduk'
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
