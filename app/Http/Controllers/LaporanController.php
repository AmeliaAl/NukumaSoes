<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanProduksi;
use App\Models\BahanBaku;
use App\Models\StokBahanBaku;
use App\Models\Produk;
use App\Models\PemakaianBahanBaku;
use App\Models\BiayaTenagaKerja;
use App\Models\BiayaOverheadPabrik;
use App\Models\Akun;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Display a listing of completed job orders (Laporan Biaya Produksi)
     */
    public function index(Request $request)
    {
        $query = PermintaanProduksi::with('produk')
            ->where('status', 'selesai')
            ->orderBy('tanggal_mulai', 'desc');

        // Filter by date range
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_mulai', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_mulai', '<=', $request->tanggal_akhir);
        }

        // Filter by product
        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        $jobOrders = $query->paginate(15);
        $produkList = Produk::orderBy('nama_produk')->get();

        return view('laporan.biaya-produksi.index', compact('jobOrders', 'produkList'));
    }

    /**
     * Display the specified job order detail
     */
    public function show($id)
    {
        $jobOrder = PermintaanProduksi::with([
            'produk',
            'admin',
            'pemakaianBahanBaku.bahanBaku',
            'pemakaianBahanBaku.stokBahanBaku',
            'biayaTenagaKerja.tenagaKerja',
            'biayaOverheadPabrik'
        ])->findOrFail($id);

        return view('laporan.biaya-produksi.show', compact('jobOrder'));
    }

    /**
     * Display job order cost card (Kartu Biaya)
     */
    public function kartuBiaya($id)
    {
        $jobOrder = PermintaanProduksi::with([
            'produk',
            'admin',
            'pemakaianBahanBaku.bahanBaku',
            'pemakaianBahanBaku.stokBahanBaku',
            'biayaTenagaKerja.tenagaKerja',
            'biayaOverheadPabrik'
        ])->findOrFail($id);

        return view('laporan.biaya-produksi.kartu-biaya', compact('jobOrder'));
    }

    /**
     * Export job order to PDF
     */
    public function exportPdf($id)
    {
        $jobOrder = PermintaanProduksi::with([
            'produk',
            'admin',
            'pemakaianBahanBaku.bahanBaku',
            'pemakaianBahanBaku.stokBahanBaku',
            'biayaTenagaKerja.tenagaKerja',
            'biayaOverheadPabrik'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('laporan.biaya-produksi.pdf', compact('jobOrder'));
        
        return $pdf->download('Kartu-Biaya-' . $jobOrder->nomor_job . '.pdf');
    }

    /**
     * Display summary report (Laporan Ringkasan)
     */
    public function summary(Request $request)
    {
        // Default periode: bulan ini
        $tanggal_mulai = $request->input('tanggal_mulai', date('Y-m-01'));
        $tanggal_akhir = $request->input('tanggal_akhir', date('Y-m-d'));

        // Query job order selesai dalam periode
        $jobOrders = PermintaanProduksi::where('status', 'selesai')
            ->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_akhir])
            ->get();

        // Summary Cards
        $total_job_order = $jobOrders->count();
        $total_unit = $jobOrders->sum('jumlah_produksi');
        $total_biaya = $jobOrders->sum('total_biaya_produksi');
        $rata_hpp = $total_unit > 0 ? $total_biaya / $total_unit : 0;

        // Breakdown Biaya
        $total_biaya_bahan = $jobOrders->sum('total_biaya_bahan');
        $total_biaya_tk = $jobOrders->sum('total_biaya_tenaga_kerja');
        $total_biaya_overhead = $jobOrders->sum('total_biaya_overhead');

        // Persediaan
        $total_jenis_bahan = BahanBaku::count();
        
        // Nilai persediaan
        $nilai_persediaan = StokBahanBaku::join('bahan_baku', 'stok_bahan_baku.id_bahan', '=', 'bahan_baku.id_bahan')
            ->sum(DB::raw('stok_bahan_baku.sisa_stok * stok_bahan_baku.harga_per_satuan'));

        // Stok alert
        $stok_menipis = BahanBaku::whereRaw('stok_saat_ini <= stok_minimum')->count();
        $stok_aman = BahanBaku::whereRaw('stok_saat_ini > stok_minimum')->count();

        // Top 5 Produk Terlaris
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

        // Manual mapping
        $top_produk = $top_produk_raw->map(function($item) {
            return (object)[
                'id_produk' => $item->id_produk,
                'nama_produk' => $item->nama_produk,
                'satuan_produk' => $item->satuan_produk,
                'total_qty' => $item->total_qty,
                'total_biaya' => floatval($item->biaya_sum ?? 0),
                'rata_hpp' => floatval($item->hpp_avg ?? 0),
            ];
        });

        // Trend Bulanan
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

        // Manual mapping
        $trend_bulanan = $trend_bulanan_raw->map(function($item) {
            return (object)[
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

    /**
     * Display variance analysis (Analisis Varians)
     */
    public function analisisVarians($id)
    {
        $jobOrder = PermintaanProduksi::with([
            'produk',
            'pemakaianBahanBaku.bahanBaku',
            'biayaTenagaKerja.tenagaKerja',
            'biayaOverheadPabrik'
        ])->findOrFail($id);

        // Hitung variance untuk bahan baku
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
                'status' => $selisih > 0 ? 'unfavorable' : 'favorable'
            ];
        }

        // Hitung variance untuk tenaga kerja
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
                'status' => $selisih > 0 ? 'unfavorable' : 'favorable'
            ];
        }

        // Total variance
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

    /**
     * Display Neraca Lajur (Worksheet) report
     */
    public function neracaSaldo(Request $request)
    {
        // Default periode: bulan ini
        $periode = $request->get('periode', date('Y-m'));
        $parts = explode('-', $periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        // Tanggal akhir periode (kumulatif sampai akhir bulan ini)
        $tanggalAkhir = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');

        $akuns = Akun::where('status', 'aktif')->orderBy('kode_akun', 'asc')->get();
        
        $totalNsDebit = 0;
        $totalNsKredit = 0;
        $totalNsdDebit = 0;
        $totalNsdKredit = 0;
        $totalLrDebit = 0;
        $totalLrKredit = 0;
        $totalNDebit = 0;
        $totalNKredit = 0;
        
        $neracaData = $akuns->map(function($akun) use ($tanggalAkhir, &$totalNsDebit, &$totalNsKredit, &$totalNsdDebit, &$totalNsdKredit, &$totalLrDebit, &$totalLrKredit, &$totalNDebit, &$totalNKredit) {
            // Hitung mutasi kumulatif debit & kredit dari awal hingga tanggalAkhir
            $debitMutasi = JurnalUmumDetail::where('id_akun', $akun->id_akun)
                ->whereHas('jurnalUmum', function($q) use ($tanggalAkhir) {
                    $q->where('tanggal', '<=', $tanggalAkhir);
                })->sum('debit');

            $kreditMutasi = JurnalUmumDetail::where('id_akun', $akun->id_akun)
                ->whereHas('jurnalUmum', function($q) use ($tanggalAkhir) {
                    $q->where('tanggal', '<=', $tanggalAkhir);
                })->sum('kredit');

            // Saldo Kumulatif
            $debit = 0;
            $kredit = 0;
            
            if ($akun->saldo_normal === 'debit') {
                $saldo = floatval($debitMutasi) - floatval($kreditMutasi);
                if ($saldo >= 0) {
                    $debit = $saldo;
                } else {
                    $kredit = abs($saldo);
                }
            } else { // saldo normal kredit
                $saldo = floatval($kreditMutasi) - floatval($debitMutasi);
                if ($saldo >= 0) {
                    $kredit = $saldo;
                } else {
                    $debit = abs($saldo);
                }
            }
            
            $totalNsDebit += $debit;
            $totalNsKredit += $kredit;

            // Penyesuaian (Sementara 0 karena belum ada fitur penyesuaian)
            $peny_debit = 0;
            $peny_kredit = 0;

            // NSD (Neraca Saldo Setelah Penyesuaian)
            $nsd_debit = $debit;
            $nsd_kredit = $kredit;

            $totalNsdDebit += $nsd_debit;
            $totalNsdKredit += $nsd_kredit;

            // Laba Rugi vs Neraca Classification
            $lr_debit = 0;
            $lr_kredit = 0;
            $n_debit = 0;
            $n_kredit = 0;

            // Akun Nominal (Pendapatan & Beban: kode awal 4, 5, 6, 7, atau tipe_akun pendapatan/beban)
            if (in_array($akun->tipe_akun, ['pendapatan', 'beban']) || in_array(substr($akun->kode_akun, 0, 1), ['4', '5', '6', '7'])) {
                $lr_debit = $nsd_debit;
                $lr_kredit = $nsd_kredit;
                $totalLrDebit += $lr_debit;
                $totalLrKredit += $lr_kredit;
            } else { // Akun Riil (Aset, Kewajiban, Ekuitas)
                $n_debit = $nsd_debit;
                $n_kredit = $nsd_kredit;
                $totalNDebit += $n_debit;
                $totalNKredit += $n_kredit;
            }
            
            return (object)[
                'kode_akun' => $akun->kode_akun,
                'nama_akun' => $akun->nama_akun,
                'ns_debit' => $debit,
                'ns_kredit' => $kredit,
                'peny_debit' => $peny_debit,
                'peny_kredit' => $peny_kredit,
                'nsd_debit' => $nsd_debit,
                'nsd_kredit' => $nsd_kredit,
                'lr_debit' => $lr_debit,
                'lr_kredit' => $lr_kredit,
                'n_debit' => $n_debit,
                'n_kredit' => $n_kredit,
            ];
        });

        // Hitung Selisih Laba / Rugi Bersih
        $selisihLr = 0;
        $labaBersih = 0;
        $rugiBersih = 0;

        if ($totalLrKredit > $totalLrDebit) {
            $labaBersih = $totalLrKredit - $totalLrDebit;
        } elseif ($totalLrDebit > $totalLrKredit) {
            $rugiBersih = $totalLrDebit - $totalLrKredit;
        }
        
        return view('laporan.neraca-saldo', compact(
            'neracaData', 
            'totalNsDebit', 'totalNsKredit', 
            'totalNsdDebit', 'totalNsdKredit', 
            'totalLrDebit', 'totalLrKredit', 
            'totalNDebit', 'totalNKredit',
            'labaBersih', 'rugiBersih',
            'periode'
        ));
    }


    /**
     * Display listing of Jurnal Umum
     */
    public function jurnalUmum(Request $request)
    {
        $query = JurnalUmum::with(['detail.akun', 'admin'])->orderBy('tanggal', 'desc')->orderBy('id_jurnal', 'desc');

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
        } elseif ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', $request->tanggal_mulai);
        }

        $jurnals = $query->paginate(20)->withQueryString();

        return view('laporan.jurnal-umum.index', compact('jurnals'));
    }

    /**
     * Display detail of a Jurnal Umum entry
     */
    public function jurnalUmumShow($id)
    {
        $jurnal = JurnalUmum::with(['detail.akun', 'admin'])->findOrFail($id);
        
        // Cek referensi untuk link ke transaksi asal
        $routeReferensi = '#';
        if ($jurnal->tipe_referensi == 'penerimaan_bahan_baku') {
            $routeReferensi = route('penerimaan-bahan-baku.show', $jurnal->id_referensi);
        } elseif ($jurnal->tipe_referensi == 'permintaan_produksi') {
            $routeReferensi = route('permintaan-produksi.show', $jurnal->id_referensi);
        } elseif ($jurnal->tipe_referensi == 'pemakaian_bahan_baku') {
            $routeReferensi = route('pemakaian-bahan-baku.index');
        } elseif ($jurnal->tipe_referensi == 'biaya_overhead_pabrik') {
            $routeReferensi = route('biaya-overhead-pabrik.index');
        } elseif ($jurnal->tipe_referensi == 'pengeluaran_bop_aktual') {
            $routeReferensi = route('pengeluaran-bop.index');
        } elseif ($jurnal->tipe_referensi == 'biaya_tenaga_kerja') {
            $routeReferensi = route('biaya-tenaga-kerja.index');
        } elseif ($jurnal->tipe_referensi == 'insentif_mingguan') {
            $routeReferensi = route('kehadiran.rekap-mingguan');
        }
        
        return view('laporan.jurnal-umum.show', compact('jurnal', 'routeReferensi'));
    }

    /**
     * Display Buku Besar
     */
    public function bukuBesar(Request $request)
    {
        $akuns = Akun::where('status', 'aktif')->orderBy('kode_akun', 'asc')->get();
        $selectedAkun = null;
        $jurnalDetails = [];
        $saldoAwal = 0;
        
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'));

        if ($request->filled('id_akun')) {
            $selectedAkun = Akun::findOrFail($request->id_akun);
            
            // Hitung saldo awal (Sebelum tanggal mulai)
            $saldoAwalDebit = JurnalUmumDetail::where('id_akun', $selectedAkun->id_akun)
                                              ->whereHas('jurnalUmum', function($q) use ($tanggalMulai) {
                                                  $q->where('tanggal', '<', $tanggalMulai);
                                              })->sum('debit');
                                              
            $saldoAwalKredit = JurnalUmumDetail::where('id_akun', $selectedAkun->id_akun)
                                              ->whereHas('jurnalUmum', function($q) use ($tanggalMulai) {
                                                  $q->where('tanggal', '<', $tanggalMulai);
                                              })->sum('kredit');
                                              
            if ($selectedAkun->saldo_normal === 'debit') {
                $saldoAwal = $saldoAwalDebit - $saldoAwalKredit;
            } else {
                $saldoAwal = $saldoAwalKredit - $saldoAwalDebit;
            }

            // Ambil mutasi pada periode
            $jurnalDetails = JurnalUmumDetail::with(['jurnalUmum'])
                                             ->where('id_akun', $selectedAkun->id_akun)
                                             ->whereHas('jurnalUmum', function($q) use ($tanggalMulai, $tanggalAkhir) {
                                                 $q->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
                                             })
                                             ->get()
                                             ->sortBy(function($detail) {
                                                 return $detail->jurnalUmum->tanggal->format('Y-m-d') . '-' . $detail->id_jurnal;
                                             });
        }

        return view('laporan.buku-besar.index', compact(
            'akuns', 'selectedAkun', 'jurnalDetails', 'saldoAwal', 'tanggalMulai', 'tanggalAkhir'
        ));
    }
}
