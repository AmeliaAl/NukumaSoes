<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiayaOverheadPabrik;
use App\Models\PermintaanProduksi;
use App\Models\KategoriBop;
use App\Models\JurnalUmum;
use App\Models\Akun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BiayaOverheadPabrikController extends Controller
{
    public function index()
    {
        $manualOverhead = BiayaOverheadPabrik::with(['permintaanProduksi.produk', 'admin'])
                                            ->get()
                                            ->map(function ($item) {
                                                return (object) [
                                                    'id' => $item->id_biaya_overhead,
                                                    'tanggal' => $item->tanggal_overhead ?? $item->created_at,
                                                    'nomor_job' => $item->permintaanProduksi->nomor_job ?? '-',
                                                    'id_permintaan_produksi' => $item->id_permintaan_produksi,
                                                    'nama_produk' => $item->permintaanProduksi->produk->nama_produk ?? '-',
                                                    'jenis_overhead' => $item->jenis_overhead,
                                                    'badge_class' => 'bg-info text-dark',
                                                    'satuan' => $item->satuan_periode,
                                                    'satuan_label' => $item->satuan_periode_label ?? '',
                                                    'nominal' => $item->nominal,
                                                    'jumlah' => $item->jumlah_batch ?? ($item->jumlah_periode ?? 1),
                                                    'total_biaya' => $item->total_biaya ?? $item->nominal,
                                                    'keterangan' => $item->keterangan ?? '-',
                                                    'is_otomatis' => false,
                                                    'tipe' => 'manual',
                                                    'id_jurnal_aktual' => $item->id_jurnal_aktual,
                                                ];
                                            });

        $btktlOverhead = \App\Models\BiayaTenagaKerja::whereHas('tenagaKerja', fn($q) => $q->where('jenis_tenaga', 'tidak_langsung'))
                                            ->with(['permintaanProduksi.produk', 'tenagaKerja', 'admin'])
                                            ->get()
                                            ->map(function ($item) {
                                                return (object) [
                                                    'id' => $item->id_biaya_tk,
                                                    'tanggal' => $item->tanggal_kerja ?? $item->created_at,
                                                    'nomor_job' => $item->permintaanProduksi->nomor_job ?? '-',
                                                    'id_permintaan_produksi' => $item->id_permintaan_produksi,
                                                    'nama_produk' => $item->permintaanProduksi->produk->nama_produk ?? '-',
                                                    'jenis_overhead' => 'BTK Tidak Langsung (BTKTL)',
                                                    'badge_class' => 'bg-dark text-white',
                                                    'satuan' => 'per_jam',
                                                    'satuan_label' => 'Jam Kerja',
                                                    'nominal' => $item->upah_per_jam,
                                                    'jumlah' => $item->jam_kerja,
                                                    'total_biaya' => $item->total_biaya,
                                                    'keterangan' => 'Gaji ' . ($item->tenagaKerja->nama_tenaga ?? '-') . ' (' . ($item->tenagaKerja->jabatan ?? '-') . ')',
                                                    'is_otomatis' => true,
                                                    'tipe' => 'btktl',
                                                ];
                                            });

        $bahanTidakLangsung = \App\Models\PemakaianBahanBaku::whereHas('bahanBaku', fn($q) => $q->where('jenis_bahan', 'tidak_langsung'))
                                            ->with(['permintaanProduksi.produk', 'bahanBaku', 'admin'])
                                            ->get()
                                            ->map(function ($item) {
                                                return (object) [
                                                    'id' => $item->id_pemakaian,
                                                    'tanggal' => $item->tanggal_pemakaian ?? $item->created_at,
                                                    'nomor_job' => $item->permintaanProduksi->nomor_job ?? '-',
                                                    'id_permintaan_produksi' => $item->id_permintaan_produksi,
                                                    'nama_produk' => $item->permintaanProduksi->produk->nama_produk ?? '-',
                                                    'jenis_overhead' => 'Bahan Penolong / BOP',
                                                    'badge_class' => 'bg-success text-white',
                                                    'satuan' => 'bahan_baku',
                                                    'satuan_label' => $item->bahanBaku->satuan ?? '',
                                                    'nominal' => $item->harga_satuan,
                                                    'jumlah' => $item->jumlah_pakai,
                                                    'total_biaya' => $item->total_biaya,
                                                    'keterangan' => 'Pemakaian ' . ($item->bahanBaku->nama_bahan ?? '-') . ' sebagai bahan penolong/BOP',
                                                    'is_otomatis' => true,
                                                    'tipe' => 'bahan_tidak_langsung',
                                                ];
                                            });

        $biayaOverhead = $manualOverhead->concat($btktlOverhead)->concat($bahanTidakLangsung)
                                        ->sortByDesc(fn($item) => \Carbon\Carbon::parse($item->tanggal)->timestamp);

        return view('transaksi.biaya-overhead-pabrik.index', compact('biayaOverhead'));
    }

    public function create()
    {
        $jobOrders = PermintaanProduksi::whereIn('status', ['pending', 'proses'])
                                      ->with('produk')
                                      ->get();
                                      
        $totalBatchAktif = BiayaOverheadPabrik::totalBatchAktif();
        $categories = KategoriBop::productionScope()->orderBy('nama_kategori')->get();

        return view('transaksi.biaya-overhead-pabrik.create', compact('jobOrders', 'totalBatchAktif', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_permintaan_produksi' => 'required|exists:permintaan_produksi,id_permintaan_produksi',
            'tanggal_overhead' => 'required|date',
            'id_kategori_bop' => 'required|exists:kategori_bop,id_kategori_bop',
            'satuan_periode' => 'required|in:per_batch,per_hari,per_minggu,per_bulan',
            'nominal' => 'required|numeric|min:0.01',
            'jumlah_periode' => 'required|integer|min:1',
            'total_nominal_global' => 'nullable|numeric|min:0',
            'jumlah_batch_terlibat' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string',
        ], [
            'id_permintaan_produksi.required' => 'Job Order harus dipilih',
            'tanggal_overhead.required' => 'Tanggal harus diisi',
            'id_kategori_bop.required' => 'Kategori BOP harus dipilih',
            'satuan_periode.required' => 'Satuan periode harus dipilih',
            'nominal.required' => 'Nominal harus diisi',
            'nominal.min' => 'Nominal minimal 0.01',
            'jumlah_periode.required' => 'Jumlah periode harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $kategori = KategoriBop::findOrFail($request->id_kategori_bop);
            $this->ensureProductionCategory($kategori);
            
            $overhead = BiayaOverheadPabrik::create([
                'id_permintaan_produksi' => $request->id_permintaan_produksi,
                'id_admin' => Auth::guard('admin')->id(),
                'id_kategori_bop' => $request->id_kategori_bop,
                'tanggal_overhead' => $request->tanggal_overhead,
                'jenis_overhead' => $kategori->nama_kategori,
                'satuan_periode' => $request->satuan_periode,
                'jumlah_batch' => $request->jumlah_periode,
                'nominal' => $request->nominal,
                'total_nominal_global' => $request->total_nominal_global,
                'jumlah_batch_terlibat' => $request->jumlah_batch_terlibat,
                'keterangan' => $request->keterangan,
            ]);

            // Update total biaya di job order
            $job = PermintaanProduksi::findOrFail($request->id_permintaan_produksi);
            $job->hitungTotalBiayaProduksi();

            // JURNAL OTOMATIS: Biaya Overhead Pabrik
            $accountingService = app(\App\Services\AccountingService::class);
            $accountingService->recordBiayaOverhead($overhead, $job);

            DB::commit();

            return redirect()->route('biaya-overhead-pabrik.index')
                           ->with('success', 'Biaya overhead berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        // FIXED: Parameter $id adalah id_overhead bukan id_biaya_overhead
        $overhead = BiayaOverheadPabrik::with(['permintaanProduksi'])->findOrFail($id);

        if ($overhead->permintaanProduksi->status === 'selesai') {
            return redirect()->route('biaya-overhead-pabrik.index')
                           ->with('error', 'Biaya dari job yang sudah selesai tidak bisa diedit!');
        }

        $jobOrders = PermintaanProduksi::whereIn('status', ['pending', 'proses'])
                                      ->with('produk')
                                      ->get();
                                      
        $totalBatchAktif = BiayaOverheadPabrik::totalBatchAktif();
        $categories = KategoriBop::productionScope()->orderBy('nama_kategori')->get();

        return view('transaksi.biaya-overhead-pabrik.edit', compact('overhead', 'jobOrders', 'totalBatchAktif', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $overhead = BiayaOverheadPabrik::findOrFail($id);

        if ($overhead->permintaanProduksi->status === 'selesai') {
            return back()->with('error', 'Biaya dari job yang sudah selesai tidak bisa diedit!');
        }

        $request->validate([
            'tanggal_overhead' => 'required|date',
            'id_kategori_bop' => 'required|exists:kategori_bop,id_kategori_bop',
            'satuan_periode' => 'required|in:per_batch,per_hari,per_minggu,per_bulan',
            'nominal' => 'required|numeric|min:0.01',
            'jumlah_periode' => 'required|integer|min:1',
            'total_nominal_global' => 'nullable|numeric|min:0',
            'jumlah_batch_terlibat' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string',
        ], [
            'tanggal_overhead.required' => 'Tanggal harus diisi',
            'id_kategori_bop.required' => 'Kategori BOP harus dipilih',
            'satuan_periode.required' => 'Satuan periode harus dipilih',
            'nominal.required' => 'Nominal harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $kategori = KategoriBop::findOrFail($request->id_kategori_bop);
            $this->ensureProductionCategory($kategori);
            
            // Cari dan hapus jurnal umum lama beserta detailnya
            $oldJurnals = JurnalUmum::where('id_referensi', $overhead->id_overhead)
                                    ->where('tipe_referensi', 'biaya_overhead_pabrik')
                                    ->get();
            foreach ($oldJurnals as $jurnal) {
                foreach ($jurnal->detail as $detail) {
                    $akun = $detail->akun;
                    if ($akun) {
                        if ($akun->saldo_normal === 'debit') {
                            $akun->saldo -= $detail->debit;
                            $akun->saldo += $detail->kredit;
                        } else {
                            $akun->saldo -= $detail->kredit;
                            $akun->saldo += $detail->debit;
                        }
                        $akun->save();
                    }
                }
                $jurnal->delete();
            }

            $overhead->update([
                'tanggal_overhead' => $request->tanggal_overhead,
                'id_kategori_bop' => $request->id_kategori_bop,
                'jenis_overhead' => $kategori->nama_kategori,
                'satuan_periode' => $request->satuan_periode,
                'jumlah_batch' => $request->jumlah_periode,
                'nominal' => $request->nominal,
                'total_nominal_global' => $request->total_nominal_global,
                'jumlah_batch_terlibat' => $request->jumlah_batch_terlibat,
                'keterangan' => $request->keterangan,
            ]);

            $job = $overhead->permintaanProduksi;
            $job->hitungTotalBiayaProduksi();

            // Buat jurnal baru dengan nominal yang baru
            $accountingService = app(\App\Services\AccountingService::class);
            $accountingService->recordBiayaOverhead($overhead, $job);

            // Update saldo akun berjalan
            $akuns = Akun::all();
            foreach ($akuns as $akun) {
                $akun->saldo = $akun->hitungSaldo();
                $akun->save();
            }

            DB::commit();

            return redirect()->route('biaya-overhead-pabrik.index')
                           ->with('success', 'Biaya overhead berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $overhead = BiayaOverheadPabrik::findOrFail($id);

        if ($overhead->permintaanProduksi->status === 'selesai') {
            return back()->with('error', 'Biaya dari job yang sudah selesai tidak bisa dihapus!');
        }

        DB::beginTransaction();
        try {
            $job = $overhead->permintaanProduksi;
            
            // Cari dan hapus jurnal umum lama beserta detailnya
            $oldJurnals = JurnalUmum::where('id_referensi', $overhead->id_overhead)
                                    ->where('tipe_referensi', 'biaya_overhead_pabrik')
                                    ->get();
            foreach ($oldJurnals as $jurnal) {
                foreach ($jurnal->detail as $detail) {
                    $akun = $detail->akun;
                    if ($akun) {
                        if ($akun->saldo_normal === 'debit') {
                            $akun->saldo -= $detail->debit;
                            $akun->saldo += $detail->kredit;
                        } else {
                            $akun->saldo -= $detail->kredit;
                            $akun->saldo += $detail->debit;
                        }
                        $akun->save();
                    }
                }
                $jurnal->delete();
            }

            $overhead->delete();

            $job->hitungTotalBiayaProduksi();

            // Update saldo akun berjalan
            $akuns = Akun::all();
            foreach ($akuns as $akun) {
                $akun->saldo = $akun->hitungSaldo();
                $akun->save();
            }

            DB::commit();

            return redirect()->route('biaya-overhead-pabrik.index')
                           ->with('success', 'Biaya overhead berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function ensureProductionCategory(KategoriBop $kategori): void
    {
        if (! KategoriBop::isAssetRelatedName($kategori->nama_kategori)) {
            return;
        }

        throw ValidationException::withMessages([
            'id_kategori_bop' => 'Kategori BOP terkait mesin/aset tidak masuk lingkup aplikasi produksi. Gunakan kategori biaya operasional seperti Gas, Listrik, atau Air, lalu isi keterangan mesin yang digunakan.',
        ]);
    }
}
