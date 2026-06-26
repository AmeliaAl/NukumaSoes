<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\BiayaOverheadPabrik;
use App\Models\BiayaTenagaKerja;
use App\Models\PemakaianBahanBaku;
use App\Models\PermintaanProduksi;
use App\Models\Produk;
use App\Models\StokBahanBaku;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Display summary report. Route currently disabled, retained for report grouping.
     */
    public function summary(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $tanggal_mulai = $request->input('tanggal_mulai', date('Y-m-01'));
        $tanggal_akhir = $request->input('tanggal_akhir', date('Y-m-d'));

        $jobOrders = PermintaanProduksi::where('status', 'selesai')
            ->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_akhir])
            ->get();

        $total_job_order = $jobOrders->count();
        $total_unit = $jobOrders->sum('jumlah_produksi');
        $total_biaya = $jobOrders->sum('total_biaya_produksi');
        $rata_hpp = $total_unit > 0 ? $total_biaya / $total_unit : 0;

        $total_biaya_bahan = $jobOrders->sum('total_biaya_bahan');
        $total_biaya_tk = $jobOrders->sum('total_biaya_tenaga_kerja');
        $total_biaya_overhead = $jobOrders->sum('total_biaya_overhead');

        $total_jenis_bahan = BahanBaku::count();
        $nilai_persediaan = StokBahanBaku::join('bahan_baku', 'stok_bahan_baku.id_bahan', '=', 'bahan_baku.id_bahan')
            ->sum(DB::raw('stok_bahan_baku.sisa_stok * stok_bahan_baku.harga_per_satuan'));

        $stok_menipis = BahanBaku::whereRaw('stok_saat_ini <= stok_minimum')->count();
        $stok_aman = BahanBaku::whereRaw('stok_saat_ini > stok_minimum')->count();

        $top_produk_raw = PermintaanProduksi::select(
                'produk.id_produk',
                'produk.nama_produk',
                'produk.satuan_produk',
                DB::raw('SUM(permintaan_produksi.jumlah_produksi) as total_qty'),
                DB::raw('SUM(permintaan_produksi.total_biaya_produksi) as biaya_sum'),
                DB::raw('AVG(permintaan_produksi.harga_pokok_per_unit) as hpp_avg')
            )
            ->join('produk', 'permintaan_produksi.id_produk', '=', 'produk.id_produk')
            ->where('permintaan_produksi.status', 'selesai')
            ->whereBetween('permintaan_produksi.tanggal_mulai', [$tanggal_mulai, $tanggal_akhir])
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.satuan_produk')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        $top_produk = $top_produk_raw->map(function ($item) {
            return (object) [
                'id_produk' => $item->id_produk,
                'nama_produk' => $item->nama_produk,
                'satuan_produk' => $item->satuan_produk,
                'total_qty' => $item->total_qty,
                'total_biaya' => floatval($item->biaya_sum ?? 0),
                'rata_hpp' => floatval($item->hpp_avg ?? 0),
            ];
        });

        $trend_bulanan_raw = PermintaanProduksi::select(
                DB::raw("DATE_FORMAT(tanggal_mulai, '%Y-%m') as bulan_raw"),
                DB::raw("DATE_FORMAT(tanggal_mulai, '%M %Y') as bulan"),
                DB::raw('COUNT(*) as jumlah_job'),
                DB::raw('SUM(jumlah_produksi) as unit_sum'),
                DB::raw('SUM(total_biaya_produksi) as biaya_sum'),
                DB::raw('AVG(total_biaya_produksi) as biaya_avg')
            )
            ->where('status', 'selesai')
            ->where('tanggal_mulai', '>=', date('Y-m-d', strtotime('-6 months')))
            ->groupBy('bulan_raw', 'bulan')
            ->orderBy('bulan_raw', 'asc')
            ->get();

        $trend_bulanan = $trend_bulanan_raw->map(function ($item) {
            return (object) [
                'bulan_raw' => $item->bulan_raw,
                'bulan' => $item->bulan,
                'jumlah_job' => $item->jumlah_job,
                'total_unit' => floatval($item->unit_sum ?? 0),
                'total_biaya' => floatval($item->biaya_sum ?? 0),
                'rata_biaya' => floatval($item->biaya_avg ?? 0),
            ];
        });

        return view('laporan.summary', compact(
            'tanggal_mulai',
            'tanggal_akhir',
            'total_job_order',
            'total_unit',
            'total_biaya',
            'rata_hpp',
            'total_biaya_bahan',
            'total_biaya_tk',
            'total_biaya_overhead',
            'total_jenis_bahan',
            'nilai_persediaan',
            'stok_menipis',
            'stok_aman',
            'top_produk',
            'trend_bulanan'
        ));
    }

    public function analisisVarians($id)
    {
        $jobOrder = PermintaanProduksi::with([
            'produk',
            'pemakaianBahanBaku.bahanBaku',
            'biayaTenagaKerja.tenagaKerja',
            'biayaOverheadPabrik',
        ])->findOrFail($id);

        $varians_bahan = [];
        foreach ($jobOrder->pemakaianBahanBaku as $pemakaian) {
            $estimasi = $pemakaian->bahanBaku->harga_rata_rata * $pemakaian->jumlah_pakai;
            $aktual = $pemakaian->total_biaya;
            $selisih = $aktual - $estimasi;
            $persentase = $estimasi > 0 ? ($selisih / $estimasi) * 100 : 0;

            $varians_bahan[] = [
                'nama_bahan' => $pemakaian->bahanBaku->nama_bahan,
                'qty' => $pemakaian->jumlah_pakai,
                'estimasi' => $estimasi,
                'aktual' => $aktual,
                'selisih' => $selisih,
                'persentase' => $persentase,
                'status' => $selisih > 0 ? 'unfavorable' : 'favorable',
            ];
        }

        $varians_tk = [];
        foreach ($jobOrder->biayaTenagaKerja as $biaya_tk) {
            $estimasi = $biaya_tk->tenagaKerja->upah_per_jam * $biaya_tk->jam_kerja;
            $aktual = $biaya_tk->total_biaya;
            $selisih = $aktual - $estimasi;
            $persentase = $estimasi > 0 ? ($selisih / $estimasi) * 100 : 0;

            $varians_tk[] = [
                'nama_tk' => $biaya_tk->tenagaKerja->nama_tenaga,
                'jam_kerja' => $biaya_tk->jam_kerja,
                'estimasi' => $estimasi,
                'aktual' => $aktual,
                'selisih' => $selisih,
                'persentase' => $persentase,
                'status' => $selisih > 0 ? 'unfavorable' : 'favorable',
            ];
        }

        $total_estimasi_bahan = collect($varians_bahan)->sum('estimasi');
        $total_aktual_bahan = collect($varians_bahan)->sum('aktual');
        $total_selisih_bahan = $total_aktual_bahan - $total_estimasi_bahan;

        $total_estimasi_tk = collect($varians_tk)->sum('estimasi');
        $total_aktual_tk = collect($varians_tk)->sum('aktual');
        $total_selisih_tk = $total_aktual_tk - $total_estimasi_tk;

        $total_estimasi = $total_estimasi_bahan + $total_estimasi_tk + $jobOrder->total_biaya_overhead;
        $total_aktual = $jobOrder->total_biaya_produksi;
        $total_selisih = $total_aktual - $total_estimasi;
        $total_persentase = $total_estimasi > 0 ? ($total_selisih / $total_estimasi) * 100 : 0;

        return view('laporan.analisis-varians', compact(
            'jobOrder',
            'varians_bahan',
            'varians_tk',
            'total_estimasi_bahan',
            'total_aktual_bahan',
            'total_selisih_bahan',
            'total_estimasi_tk',
            'total_aktual_tk',
            'total_selisih_tk',
            'total_estimasi',
            'total_aktual',
            'total_selisih',
            'total_persentase'
        ));
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
