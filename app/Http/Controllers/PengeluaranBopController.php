<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\BiayaOverheadPabrik;
use App\Models\KategoriBop;
use App\Models\PermintaanProduksi;
use App\Models\StokProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class PengeluaranBopController extends Controller
{
    /**
     * Menampilkan daftar pengeluaran BOP aktual.
     */
    public function index()
    {
        $bopBelumAktual = BiayaOverheadPabrik::with(['permintaanProduksi.produk'])
            ->belumDiaktualkan()
            ->orderBy('tanggal_overhead', 'desc')
            ->get();

        $jurnals = JurnalUmum::with(['detail.akun', 'admin', 'alokasiBopAktual'])
            ->where('tipe_referensi', 'pengeluaran_bop_aktual')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_jurnal', 'desc')
            ->paginate(15);

        return view('transaksi.pengeluaran-bop.index', compact('jurnals', 'bopBelumAktual'));
    }

    /**
     * Menampilkan form input pengeluaran BOP aktual.
     */
    public function create()
    {
        // Ambil akun untuk debit (Beban BOP / Operasional aktual)
        // Ambil BOP yang belum diaktualkan (opsional: user bisa pilih untuk di-link)
        $bopBelumAktual = BiayaOverheadPabrik::with(['permintaanProduksi.produk'])
            ->belumDiaktualkan()
            ->orderBy('tanggal_overhead', 'desc')
            ->get();

        $kategoriBop = KategoriBop::productionScope()->with('akun')->orderBy('nama_kategori')->get();
        $jobOrders = PermintaanProduksi::with(['produk', 'batchProduksi' => fn ($query) => $query
                ->whereIn('status', ['proses', 'selesai'])])
            ->whereIn('status', ['proses', 'selesai'])
            ->orderBy('tanggal_mulai')
            ->get();
        $jobOrderPreview = $jobOrders->flatMap(fn ($job) => $job->batchProduksi->map(fn ($batch) => [
            'nomor_job' => $job->nomor_job . ' / Batch ' . $batch->urutan,
            'produk' => $job->produk->nama_produk ?? '-',
            'tanggal_mulai' => $batch->tanggal_mulai?->format('Y-m-d'),
            'tanggal_selesai' => $batch->tanggal_selesai?->format('Y-m-d'),
            'jumlah_batch' => 1,
        ]))->values();
        $selectedCategoryId = optional($bopBelumAktual->firstWhere('id_overhead', (int) request('bop_id')))->id_kategori_bop;

        return view('transaksi.pengeluaran-bop.create', compact(
            'bopBelumAktual',
            'kategoriBop',
            'jobOrderPreview',
            'selectedCategoryId'
        ));
    }

    /**
     * Menyimpan pengeluaran BOP aktual dan membuat jurnal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nomor_pembayaran' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._\/-]+$/', 'unique:jurnal_umum,nomor_pembayaran'],
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'keterangan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'id_kategori_bop' => 'required|exists:kategori_bop,id_kategori_bop',
            'bop_terkait' => 'nullable|array',
            'bop_terkait.*' => 'distinct|exists:biaya_overhead_pabrik,id_overhead',
        ]);

        $kategori = KategoriBop::with('akun')->findOrFail($request->id_kategori_bop);
        $this->ensureProductionCategory($kategori);
        $akunDebit = $kategori->akun;
        if (!$akunDebit || $akunDebit->status !== 'aktif') {
            throw ValidationException::withMessages([
                'id_kategori_bop' => 'Kategori BOP belum memiliki akun COA aktif.',
            ]);
        }

        $bopTerkait = collect($request->input('bop_terkait', []))->filter();
        if ($bopTerkait->isNotEmpty() && BiayaOverheadPabrik::whereIn('id_overhead', $bopTerkait)
            ->where('id_kategori_bop', '!=', $kategori->id_kategori_bop)->exists()) {
            throw ValidationException::withMessages([
                'bop_terkait' => 'Semua BOP yang dikaitkan harus memiliki kategori yang sama dengan pembayaran.',
            ]);
        }

        // Hardcode Akun Kredit ke 212 (Hutang lainnya) karena ini batas lingkup HPP
        $akunKredit = Akun::where('kode_akun', '212')->first();
        if (!$akunKredit) {
            return back()->with('error', 'Gagal menyimpan: Akun "Hutang lainnya" (Kode: 212) tidak ditemukan.')->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. Buat Header Jurnal
            $jurnal = JurnalUmum::create([
                'tanggal' => $request->tanggal,
                'periode_mulai' => $request->periode_mulai,
                'periode_selesai' => $request->periode_selesai,
                'nomor_bukti' => JurnalUmum::generateNomorBukti('BOP'), // Pakai prefix BOP
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'keterangan' => $request->keterangan,
                'id_referensi' => null, // Tidak ada tabel sumber spesifik
                'tipe_referensi' => 'pengeluaran_bop_aktual',
                'id_admin' => 1, // Hardcode admin 1 (atau sesuaikan dengan auth()->id() jika ada)
            ]);

            // 2. Buat Detail Debit (BOP Sesungguhnya)
            JurnalUmumDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun' => $akunDebit->id_akun,
                'debit' => $request->nominal,
                'kredit' => 0,
            ]);

            // 3. Buat Detail Kredit (Hutang lainnya)
            JurnalUmumDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun' => $akunKredit->id_akun,
                'debit' => 0,
                'kredit' => $request->nominal,
            ]);

            // Update Saldo Akun Debit
            $akunDebit->saldo += $request->nominal; // Saldo normal beban adalah debit
            $akunDebit->save();

            // Update Saldo Akun Kredit (Hutang lainnya, normal kredit)
            $akunKredit->saldo += $request->nominal;
            $akunKredit->save();

            // 4. Link BOP terkait (opsional) — tandai sebagai "sudah diaktualkan"
            if ($request->has('bop_terkait') && is_array($request->bop_terkait)) {
                BiayaOverheadPabrik::whereIn('id_overhead', $request->bop_terkait)
                    ->whereNull('id_jurnal_aktual')
                    ->update(['id_jurnal_aktual' => $jurnal->id_jurnal]);
            }

            // Pembayaran tanpa BOP estimasi dialokasikan otomatis ke seluruh job dalam periode produksi.
            if ($bopTerkait->isEmpty()) {
                $this->alokasikanKeJobOrder($jurnal, $request);
            }

            DB::commit();
            return redirect()->route('pengeluaran-bop.index')
                ->with('success', 'Pengeluaran BOP Aktual berhasil disimpan dan dijurnal.');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Membatalkan/menghapus pengeluaran BOP aktual.
     */
    public function destroy($id)
    {
        $jurnal = JurnalUmum::where('tipe_referensi', 'pengeluaran_bop_aktual')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $jobIdsAlokasi = BiayaOverheadPabrik::where('id_jurnal_aktual', $jurnal->id_jurnal)
                ->where('is_alokasi_aktual', true)
                ->pluck('id_permintaan_produksi');
            $allocationIds = BiayaOverheadPabrik::where('id_jurnal_aktual', $jurnal->id_jurnal)
                ->where('is_alokasi_aktual', true)
                ->pluck('id_overhead');

            JurnalUmum::where('tipe_referensi', 'biaya_overhead_pabrik')
                ->whereIn('id_referensi', $allocationIds)
                ->get()
                ->each(fn ($allocationJournal) => $this->reverseAndDeleteJurnal($allocationJournal));

            // Kembalikan saldo akun sebelum menghapus
            $this->reverseAndDeleteJurnal($jurnal);

            // Unlink BOP yang terkait jurnal ini
            BiayaOverheadPabrik::where('id_jurnal_aktual', $jurnal->id_jurnal)
                ->where('is_alokasi_aktual', false)
                ->update(['id_jurnal_aktual' => null]);
            BiayaOverheadPabrik::where('id_jurnal_aktual', $jurnal->id_jurnal)
                ->where('is_alokasi_aktual', true)
                ->delete();

            PermintaanProduksi::whereIn('id_permintaan_produksi', $jobIdsAlokasi)
                ->get()
                ->each(function ($job) {
                    $job->hitungTotalBiayaProduksi();
                    $this->refreshCompletedJobAccounting($job);
                });
            
            DB::commit();
            return redirect()->route('pengeluaran-bop.index')
                ->with('success', 'Pengeluaran BOP Aktual berhasil dibatalkan dan jurnal ditarik kembali.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function alokasikanKeJobOrder(JurnalUmum $jurnal, Request $request): void
    {
        $jobs = PermintaanProduksi::whereIn('status', ['proses', 'selesai'])
            ->whereHas('batchProduksi', fn ($query) => $query
                ->whereIn('status', ['proses', 'selesai'])
                ->whereDate('tanggal_mulai', '<=', $request->periode_selesai)
                ->where(fn ($dateQuery) => $dateQuery->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', $request->periode_mulai)))
            ->withCount(['batchProduksi as batch_dalam_periode' => fn ($query) => $query
                ->whereIn('status', ['proses', 'selesai'])
                ->whereDate('tanggal_mulai', '<=', $request->periode_selesai)
                ->where(fn ($dateQuery) => $dateQuery->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', $request->periode_mulai))])
            ->orderBy('tanggal_mulai')
            ->orderBy('id_permintaan_produksi')
            ->get();

        if ($jobs->isEmpty()) {
            throw ValidationException::withMessages([
                'periode_mulai' => 'Tidak ada job order pada rentang periode produksi tersebut.',
            ]);
        }

        $kategori = KategoriBop::findOrFail($request->id_kategori_bop);
        $this->ensureProductionCategory($kategori);
        $totalBatch = (int) $jobs->sum('batch_dalam_periode');
        $sisaNominal = round((float) $request->nominal, 2);

        foreach ($jobs as $index => $job) {
            $isLast = $index === $jobs->count() - 1;
            $alokasi = $isLast
                ? $sisaNominal
                : round(((float) $request->nominal / $totalBatch) * $job->batch_dalam_periode, 2);
            $sisaNominal = round($sisaNominal - $alokasi, 2);

            $overhead = BiayaOverheadPabrik::create([
                'id_permintaan_produksi' => $job->id_permintaan_produksi,
                'id_admin' => auth('admin')->id() ?? 1,
                'id_kategori_bop' => $kategori->id_kategori_bop,
                'tanggal_overhead' => $jurnal->tanggal,
                'jenis_overhead' => $kategori->nama_kategori,
                'satuan_periode' => 'per_batch',
                'jumlah_batch' => $job->batch_dalam_periode,
                'nominal' => $alokasi,
                'total_nominal_global' => $request->nominal,
                'jumlah_batch_terlibat' => $totalBatch,
                'keterangan' => 'Alokasi aktual ' . $jurnal->nomor_pembayaran
                    . ' (' . Carbon::parse($request->periode_mulai)->format('d/m/Y')
                    . ' - ' . Carbon::parse($request->periode_selesai)->format('d/m/Y') . ')',
                'id_jurnal_aktual' => $jurnal->id_jurnal,
                'is_alokasi_aktual' => true,
            ]);

            $job->hitungTotalBiayaProduksi();
            app(\App\Services\AccountingService::class)->recordBiayaOverhead($overhead, $job);
            $this->refreshCompletedJobAccounting($job);
        }
    }

    private function refreshCompletedJobAccounting(PermintaanProduksi $job): void
    {
        if ($job->status !== 'selesai') return;

        JurnalUmum::where('tipe_referensi', 'permintaan_produksi')
            ->where('id_referensi', $job->id_permintaan_produksi)
            ->get()
            ->each(fn ($completionJournal) => $this->reverseAndDeleteJurnal($completionJournal));

        $job->refresh();
        StokProduk::where('id_permintaan_produksi', $job->id_permintaan_produksi)->update([
            'harga_pokok_per_unit' => $job->harga_pokok_per_unit,
            'total_nilai' => $job->jumlah_produksi * $job->harga_pokok_per_unit,
        ]);
        app(\App\Services\AccountingService::class)->recordBarangSelesai($job);
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

    private function reverseAndDeleteJurnal(JurnalUmum $jurnal): void
    {
        $jurnal->loadMissing('detail.akun');
        foreach ($jurnal->detail as $detail) {
            $akun = $detail->akun;
            if (!$akun) continue;

            if ($akun->saldo_normal === 'debit') {
                $akun->saldo -= $detail->debit;
                $akun->saldo += $detail->kredit;
            } else {
                $akun->saldo -= $detail->kredit;
                $akun->saldo += $detail->debit;
            }
            $akun->save();
        }

        JurnalUmumDetail::where('id_jurnal', $jurnal->id_jurnal)->delete();
        $jurnal->delete();
    }
}
