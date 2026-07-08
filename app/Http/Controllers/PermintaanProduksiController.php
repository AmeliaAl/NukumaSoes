<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanProduksi;
use App\Models\Produk;
use App\Models\BatchProduksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermintaanProduksiController extends Controller
{
    public function index()
    {
        $jobOrders = PermintaanProduksi::with(['produk', 'admin', 'pemakaianBahanBaku', 'biayaTenagaKerja', 'biayaOverheadPabrik'])
                                      ->orderBy('tanggal_mulai', 'desc')
                                      ->get();

        $produkList = Produk::where('status', 'aktif')->get();

        return view('transaksi.permintaan-produksi.index', compact('jobOrders', 'produkList'));
    }

    public function create()
    {
        $lastJob = PermintaanProduksi::orderBy('nomor_job', 'desc')->first();
        
        if ($lastJob) {
            $lastNumber = (int) substr($lastJob->nomor_job, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $nomor_job = 'JOB-' . date('Ymd') . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $produk = Produk::where('status', 'aktif')->get();

        return view('transaksi.permintaan-produksi.create', compact('nomor_job', 'produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_job' => 'required|string|max:50|unique:permintaan_produksi,nomor_job',
            'id_produk' => 'required|exists:produk,id_produk',
            'tanggal_mulai' => 'required|date',
            'jumlah_produksi' => 'required|numeric|min:1',
            'jumlah_batch' => 'required|integer|min:1',
            'kapasitas_batch_per_hari' => 'nullable|integer|min:1|max:50',
            'jenis_produksi' => 'required|in:maklun,brand_sendiri',
            'tujuan_produksi' => 'required|in:pesanan,stok_wip,stok_barang_jadi',
            'tahap_produksi' => 'required|in:persiapan,produksi,filling,selesai',
            'nama_customer_maklun' => 'nullable|required_if:jenis_produksi,maklun|string|max:100',
            'customer' => 'nullable|string|max:100',
            'status' => 'required|in:pending,proses',
            'keterangan' => 'nullable|string',
        ]);

        // CRITICAL FIX: Gunakan field name yang benar
        $job = PermintaanProduksi::create([
            'nomor_job' => $request->nomor_job,
            'id_produk' => $request->id_produk,
            'id_admin' => Auth::guard('admin')->id(),
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => null,
            'jumlah_produksi' => $request->jumlah_produksi,
            'jumlah_batch' => $request->jumlah_batch,
            'jenis_produksi' => $request->jenis_produksi,
            'tujuan_produksi' => $request->tujuan_produksi,
            'tahap_produksi' => $request->tahap_produksi,
            'nama_customer_maklun' => $request->nama_customer_maklun,
            'customer' => $request->customer,
            'status' => $request->status,
            'total_biaya_bahan' => 0,  // FIXED: Field name yang benar
            'total_biaya_tenaga_kerja' => 0,
            'total_biaya_overhead' => 0,
            'total_biaya_produksi' => 0,
            'harga_pokok_per_unit' => 0,
            'keterangan' => $request->keterangan,
        ]);
        $job->sinkronkanBatchRencana(
            (int) $request->jumlah_batch,
            (float) $request->jumlah_produksi,
            $request->tanggal_mulai,
            (int) $request->input('kapasitas_batch_per_hari', 1)
        );

        Log::info("Job Order Created: {$job->nomor_job}", [
            'id' => $job->id_permintaan_produksi,
            'initial_costs' => [
                'bahan' => $job->total_biaya_bahan,
                'tenaga_kerja' => $job->total_biaya_tenaga_kerja,
                'overhead' => $job->total_biaya_overhead,
            ]
        ]);

        return redirect()->route('permintaan-produksi.index')
                       ->with('success', 'Job Order berhasil dibuat!');
    }

    public function show($id)
    {
        $job = PermintaanProduksi::with([
            'produk',
            'admin',
            'pemakaianBahanBaku.bahanBaku',
            'pemakaianBahanBaku.stokBahanBaku',
            'biayaTenagaKerja.tenagaKerja',
            'biayaOverheadPabrik',
            'batchProduksi',
        ])->findOrFail($id);

        // CRITICAL FIX: Auto-recalculate saat page load
        if ($job->status != 'selesai') {
            $biayaBreakdown = $job->hitungTotalBiayaProduksi();
            $job->refresh();
            
            Log::info("Job Order Show - Auto Recalculate: {$job->nomor_job}", [
                'breakdown' => $biayaBreakdown,
            ]);
        }

        return view('transaksi.permintaan-produksi.show', compact('job'));
    }

    public function edit($id)
    {
        $job = PermintaanProduksi::findOrFail($id);

        if ($job->status === 'selesai') {
            return redirect()->route('permintaan-produksi.index')
                           ->with('error', 'Job Order yang sudah selesai tidak bisa diedit!');
        }

        $produk = Produk::where('status', 'aktif')->get();

        return view('transaksi.permintaan-produksi.edit', compact('job', 'produk'));
    }

    public function update(Request $request, $id)
    {
        $job = PermintaanProduksi::findOrFail($id);

        if ($job->status === 'selesai') {
            return back()->with('error', 'Job Order yang sudah selesai tidak bisa diedit!');
        }

        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'tanggal_mulai' => 'required|date',
            'jumlah_produksi' => 'required|numeric|min:1',
            'jumlah_batch' => 'required|integer|min:1',
            'kapasitas_batch_per_hari' => 'nullable|integer|min:1|max:50',
            'jenis_produksi' => 'required|in:maklun,brand_sendiri',
            'tujuan_produksi' => 'required|in:pesanan,stok_wip,stok_barang_jadi',
            'tahap_produksi' => 'required|in:persiapan,produksi,filling,selesai',
            'nama_customer_maklun' => 'nullable|required_if:jenis_produksi,maklun|string|max:100',
            'customer' => 'nullable|string|max:100',
            'status' => 'required|in:pending,proses',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $job->update([
            'id_produk' => $request->id_produk,
            'tanggal_mulai' => $request->tanggal_mulai,
            'jumlah_produksi' => $request->jumlah_produksi,
            'jumlah_batch' => $request->jumlah_batch,
            'jenis_produksi' => $request->jenis_produksi,
            'tujuan_produksi' => $request->tujuan_produksi,
            'tahap_produksi' => $request->tahap_produksi,
            'nama_customer_maklun' => $request->nama_customer_maklun,
            'customer' => $request->customer,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            ]);
            $job->sinkronkanBatchRencana(
                (int) $request->jumlah_batch,
                (float) $request->jumlah_produksi,
                $request->tanggal_mulai,
                (int) $request->input('kapasitas_batch_per_hari', 1)
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['jumlah_batch' => $e->getMessage()])->withInput();
        }

        return redirect()->route('permintaan-produksi.index')
                       ->with('success', 'Job Order berhasil diupdate!');
    }

    public function destroy($id)
    {
        $job = PermintaanProduksi::findOrFail($id);

        if ($job->pemakaianBahanBaku()->count() > 0 || 
            $job->biayaTenagaKerja()->count() > 0 || 
            $job->biayaOverheadPabrik()->count() > 0) {
            return back()->with('error', 'Job Order tidak dapat dihapus karena sudah ada transaksi terkait!');
        }

        $nomorJob = $job->nomor_job;
        $job->delete();

        Log::info("Job Order Deleted: {$nomorJob}");

        return redirect()->route('permintaan-produksi.index')
                       ->with('success', 'Job Order berhasil dihapus!');
    }

    /**
     * CRITICAL FIX: Complete job order dengan proper calculation
     */
    public function complete($id)
    {
        $job = PermintaanProduksi::with('batchProduksi')->findOrFail($id);

        if ($job->status === 'selesai') {
            return back()->with('error', 'Job Order ini sudah selesai!');
        }

        if ($job->batchProduksi->isEmpty()) {
            return back()->with('error', 'Job Order belum memiliki rincian batch.');
        }

        if ($job->batchProduksi->contains(fn ($batch) => !in_array($batch->status, ['selesai', 'dibatalkan'], true))) {
            return back()->with('error', 'Batch yang belum dipakai harus diselesaikan atau dibatalkan terlebih dahulu.');
        }

        $totalHasil = (float) $job->batchProduksi
            ->where('status', '!=', 'dibatalkan')
            ->sum('jumlah_hasil');
        if ($totalHasil <= 0) {
            return back()->with('error', 'Hasil aktual seluruh batch harus lebih dari 0.');
        }

        $targetProduksi = (float) $job->jumlah_produksi;
        if (abs($totalHasil - $targetProduksi) > 0.01) {
            return back()->with('error', 'Total hasil batch harus sama dengan target produksi. Jika kurang, tambah batch tambahan. Jika lebih, koreksi hasil batch.');
        }

        DB::beginTransaction();
        try {
            $job->status = 'selesai';
            $job->tahap_produksi = 'selesai';
            $job->tanggal_selesai = now();
            $job->jumlah_produksi = $totalHasil;
            $job->save();
            
            // Hitung total biaya produksi
            $biayaBreakdown = $job->hitungTotalBiayaProduksi();

            // Buat record stok produk (WIP/Jadi)
            $job->buatStokProduk();
            
            // Refresh untuk get data terbaru
            $job->refresh();
            
            // JURNAL OTOMATIS: Barang Selesai (WIP / Produk Jadi)
            $accountingService = app(\App\Services\AccountingService::class);
            $accountingService->recordBarangSelesai($job);
            
            Log::info("Job Order Completed: {$job->nomor_job}", [
                'breakdown' => $biayaBreakdown,
                'final_values' => [
                    'total_biaya_bahan' => $job->total_biaya_bahan,
                    'total_biaya_tenaga_kerja' => $job->total_biaya_tenaga_kerja,
                    'total_biaya_overhead' => $job->total_biaya_overhead,
                    'total_biaya_produksi' => $job->total_biaya_produksi,
                    'harga_pokok_per_unit' => $job->harga_pokok_per_unit,
                ]
            ]);

            DB::commit();

            return redirect()->route('permintaan-produksi.show', $job->id_permintaan_produksi)
                           ->with('success', 'Job Order berhasil diselesaikan! Total biaya: Rp ' . number_format($job->total_biaya_produksi, 0, ',', '.'))
                           ->with('biaya_updated', true);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error completing job order: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyelesaikan job order: ' . $e->getMessage());
        }
    }

    public function storeBatch(Request $request, $id)
    {
        $job = PermintaanProduksi::with('batchProduksi')->findOrFail($id);
        if ($job->status === 'selesai') {
            return back()->with('error', 'Job Order yang sudah selesai tidak dapat ditambah batch.');
        }

        $sisaProduksi = $job->sisaProduksiBatch();
        if ($sisaProduksi <= 0) {
            return back()->with('error', 'Target produksi sudah terpenuhi. Batalkan batch rencana yang tidak dipakai, bukan menambah batch baru.');
        }

        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'jumlah_target' => 'nullable|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $urutan = ((int) $job->batchProduksi()->max('urutan')) + 1;
        $target = min((float) ($validated['jumlah_target'] ?? $sisaProduksi), $sisaProduksi);

        BatchProduksi::create([
            'id_permintaan_produksi' => $job->id_permintaan_produksi,
            'urutan' => $urutan,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'jumlah_target' => $target,
            'jumlah_hasil' => 0,
            'jenis_batch' => 'tambahan',
            'status' => 'rencana',
            'keterangan' => $validated['keterangan'] ?? 'Batch tambahan karena realisasi belum memenuhi target',
        ]);

        return back()->with('success', "Batch tambahan {$urutan} berhasil dibuat dengan target " . number_format($target, 2, ',', '.') . ".");
    }

    public function updateBatch(Request $request, $id, $batchId)
    {
        $job = PermintaanProduksi::findOrFail($id);
        if ($job->status === 'selesai') {
            return back()->with('error', 'Batch dari Job Order yang sudah selesai tidak dapat diubah.');
        }

        $batch = BatchProduksi::where('id_permintaan_produksi', $job->id_permintaan_produksi)
            ->findOrFail($batchId);
        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai|required_if:status,selesai',
            'jumlah_target' => 'required|numeric|min:0',
            'jumlah_hasil' => 'required|numeric|min:0|required_if:status,selesai',
            'status' => 'required|in:rencana,proses,selesai,dibatalkan',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validated['status'] === 'selesai' && (float) $validated['jumlah_hasil'] <= 0) {
            return back()->withErrors(['jumlah_hasil' => 'Batch selesai harus memiliki hasil aktual lebih dari 0.']);
        }

        if ($validated['status'] === 'dibatalkan') {
            $validated['jumlah_hasil'] = 0;
            $validated['tanggal_selesai'] = $validated['tanggal_selesai'] ?: now()->toDateString();
        }

        $totalHasilLain = (float) $job->batchProduksi()
            ->where('id_batch_produksi', '!=', $batch->id_batch_produksi)
            ->where('status', '!=', 'dibatalkan')
            ->sum('jumlah_hasil');
        $hasilBaru = $validated['status'] === 'dibatalkan' ? 0 : (float) $validated['jumlah_hasil'];
        if (($totalHasilLain + $hasilBaru) > ((float) $job->jumlah_produksi + 0.01)) {
            return back()->withErrors(['jumlah_hasil' => 'Total hasil batch tidak boleh melebihi target produksi Job Order.']);
        }

        $batch->update($validated);
        if (in_array($batch->status, ['proses', 'selesai'], true) && $job->status === 'pending') {
            $job->update(['status' => 'proses']);
        }

        return back()->with('success', "Batch {$batch->urutan} berhasil diperbarui.");
    }

    /**
     * CRITICAL FIX: Recalculate biaya dengan proper logging
     */
    public function recalculate($id)
    {
        $job = PermintaanProduksi::findOrFail($id);
        
        Log::info("Manual Recalculate Triggered: {$job->nomor_job}", [
            'before' => [
                'total_biaya_bahan' => $job->total_biaya_bahan,
                'total_biaya_produksi' => $job->total_biaya_produksi,
            ]
        ]);
        
        // Hitung ulang
        $biayaBreakdown = $job->hitungTotalBiayaProduksi();
        
        // Refresh
        $job->refresh();
        
        Log::info("Manual Recalculate Complete: {$job->nomor_job}", [
            'breakdown' => $biayaBreakdown,
            'after' => [
                'total_biaya_bahan' => $job->total_biaya_bahan,
                'total_biaya_produksi' => $job->total_biaya_produksi,
            ]
        ]);

        return redirect()->route('permintaan-produksi.show', $job->id_permintaan_produksi)
                       ->with('success', 'Biaya produksi berhasil dihitung ulang!')
                       ->with('biaya_updated', true)
                       ->with('biaya_breakdown', $biayaBreakdown);
    }
}
