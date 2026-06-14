<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemakaianBahanBaku;
use App\Models\PermintaanProduksi;
use App\Models\BahanBaku;
use App\Models\StokBahanBaku;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PemakaianBahanBakuController extends Controller
{
    public function index()
    {
        $pemakaianBahan = PemakaianBahanBaku::with(['permintaanProduksi.produk', 'bahanBaku', 'produkWip', 'stokBahanBaku', 'stokProduk', 'admin'])
                                       ->orderBy('created_at', 'desc')
                                       ->get();

        return view('transaksi.pemakaian-bahan-baku.index', compact('pemakaianBahan'));
    }

    public function create()
    {
        $jobOrders = PermintaanProduksi::whereIn('status', ['pending', 'proses'])
                                      ->with('produk')
                                      ->get();
        $bahanBaku = BahanBaku::where('status', 'aktif')->get();
        $wipProduk = Produk::where('status', 'aktif')->whereIn('tipe_produk', ['kulit', 'isi'])->get();

        // Build a unified collection of options
        $options = collect();
        foreach ($bahanBaku as $b) {
            $options->push((object)[
                'id' => 'bahan_' . $b->id_bahan,
                'nama' => $b->nama_bahan . ' (Bahan Baku)',
                'kode' => $b->kode_bahan,
                'satuan' => $b->satuan,
                'stok' => $b->stok_saat_ini,
            ]);
        }
        foreach ($wipProduk as $w) {
            $stokWip = \App\Models\StokProduk::where('id_produk', $w->id_produk)
                ->where('status', 'tersedia')
                ->sum('sisa_stok');
            $options->push((object)[
                'id' => 'wip_' . $w->id_produk,
                'nama' => $w->nama_produk . ' (WIP - ' . ucfirst($w->tipe_produk) . ')',
                'kode' => $w->kode_produk,
                'satuan' => $w->satuan_produk,
                'stok' => $stokWip,
            ]);
        }

        return view('transaksi.pemakaian-bahan-baku.create', compact('jobOrders', 'options'));
    }

    /**
     * Get FIFO batches untuk bahan/WIP tertentu (AJAX)
     */
    public function getFifoBatches($key)
    {
        try {
            $parts = explode('_', $key);
            if (count($parts) !== 2) {
                return response()->json(['success' => false, 'message' => 'Invalid key format'], 400);
            }
            $type = $parts[0];
            $idVal = $parts[1];

            $batches = [];
            if ($type === 'bahan') {
                $batches = StokBahanBaku::where('id_bahan', $idVal)
                                        ->where('status', 'tersedia')
                                        ->where('sisa_stok', '>', 0)
                                        ->orderBy('tanggal_masuk', 'asc')
                                        ->orderBy('id_stok', 'asc')
                                        ->get()
                                        ->map(function($batch) {
                                            return [
                                                'id_stok' => $batch->id_stok,
                                                'tanggal_masuk' => $batch->tanggal_masuk->format('d/m/Y'),
                                                'sisa_stok' => floatval($batch->sisa_stok),
                                                'harga_per_satuan' => floatval($batch->harga_per_satuan),
                                            ];
                                        });
            } elseif ($type === 'wip') {
                $batches = \App\Models\StokProduk::where('id_produk', $idVal)
                                        ->where('status', 'tersedia')
                                        ->where('sisa_stok', '>', 0)
                                        ->orderBy('tanggal_masuk', 'asc')
                                        ->orderBy('id_stok_produk', 'asc')
                                        ->get()
                                        ->map(function($batch) {
                                            return [
                                                'id_stok' => $batch->id_stok_produk,
                                                'tanggal_masuk' => $batch->tanggal_masuk ? $batch->tanggal_masuk->format('d/m/Y') : $batch->created_at->format('d/m/Y'),
                                                'sisa_stok' => floatval($batch->sisa_stok),
                                                'harga_per_satuan' => floatval($batch->harga_pokok_per_unit),
                                            ];
                                        });
            }

            return response()->json([
                'success' => true,
                'batches' => $batches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get BOM (kebutuhan bahan/WIP) berdasarkan Job Order (AJAX)
     */
    public function getBomForJob($id_permintaan_produksi)
    {
        try {
            $job = PermintaanProduksi::with(['produk.bomBahan.bahanBaku', 'produk.bomBahan.produkWip'])->findOrFail($id_permintaan_produksi);
            
            $boms = [];
            if ($job->produk && $job->produk->bomBahan) {
                foreach ($job->produk->bomBahan as $bom) {
                    $jumlahDibutuhkan = $bom->jumlah_kebutuhan * $job->jumlah_produksi;
                    
                    if ($bom->id_bahan) {
                        $bahan = $bom->bahanBaku;
                        if (!$bahan || $bahan->status !== 'aktif') continue;
                        
                        $boms[] = [
                            'key' => 'bahan_' . $bahan->id_bahan,
                            'id_bahan' => 'bahan_' . $bahan->id_bahan,
                            'kode_bahan' => $bahan->kode_bahan,
                            'nama_bahan' => $bahan->nama_bahan . ' (Bahan Baku)',
                            'satuan' => $bahan->satuan,
                            'stok_saat_ini' => floatval($bahan->stok_saat_ini),
                            'jumlah_kebutuhan' => $jumlahDibutuhkan,
                            'cukup' => $bahan->stok_saat_ini >= $jumlahDibutuhkan
                        ];
                    } elseif ($bom->id_produk_wip) {
                        $wip = $bom->produkWip;
                        if (!$wip || $wip->status !== 'aktif') continue;
                        
                        $stokWip = \App\Models\StokProduk::where('id_produk', $wip->id_produk)
                            ->where('status', 'tersedia')
                            ->sum('sisa_stok');
                        
                        $boms[] = [
                            'key' => 'wip_' . $wip->id_produk,
                            'id_bahan' => 'wip_' . $wip->id_produk,
                            'kode_bahan' => $wip->kode_produk,
                            'nama_bahan' => $wip->nama_produk . ' (WIP - ' . ucfirst($wip->tipe_produk) . ')',
                            'satuan' => $wip->satuan_produk,
                            'stok_saat_ini' => floatval($stokWip),
                            'jumlah_kebutuhan' => $jumlahDibutuhkan,
                            'cukup' => $stokWip >= $jumlahDibutuhkan
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $boms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail pemakaian untuk modal (AJAX)
     */
    public function getDetail($id)
    {
        try {
            $pemakaian = PemakaianBahanBaku::with([
                'permintaanProduksi.produk',
                'bahanBaku',
                'produkWip',
                'stokBahanBaku',
                'stokProduk'
            ])->findOrFail($id);

            $namaBahan = '';
            $satuan = '';
            $tanggalBatch = '-';

            if ($pemakaian->id_bahan) {
                $namaBahan = $pemakaian->bahanBaku->nama_bahan . ' (Bahan Baku)';
                $satuan = $pemakaian->bahanBaku->satuan;
                $tanggalBatch = $pemakaian->stokBahanBaku ? $pemakaian->stokBahanBaku->tanggal_masuk->format('d/m/Y') : '-';
            } elseif ($pemakaian->id_produk_wip) {
                $namaBahan = $pemakaian->produkWip->nama_produk . ' (WIP - ' . ucfirst($pemakaian->produkWip->tipe_produk) . ')';
                $satuan = $pemakaian->produkWip->satuan_produk;
                $tanggalBatch = $pemakaian->stokProduk ? ($pemakaian->stokProduk->tanggal_masuk ? $pemakaian->stokProduk->tanggal_masuk->format('d/m/Y') : $pemakaian->stokProduk->created_at->format('d/m/Y')) : '-';
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nomor_job' => $pemakaian->permintaanProduksi->nomor_job,
                    'nama_produk' => $pemakaian->permintaanProduksi->produk->nama_produk,
                    'nama_bahan' => $namaBahan,
                    'satuan' => $satuan,
                    'tanggal' => $pemakaian->created_at->format('d/m/Y H:i'),
                    'jumlah_pakai' => number_format($pemakaian->jumlah_pakai, 2),
                    'harga_satuan' => $pemakaian->harga_satuan,
                    'total_biaya' => $pemakaian->total_biaya,
                    'id_stok' => $pemakaian->id_stok ?? $pemakaian->id_stok_produk,
                    'tanggal_batch' => $tanggalBatch,
                    'keterangan' => $pemakaian->keterangan,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan MULTIPLE pemakaian bahan baku dengan FIFO Logic
     */
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'id_permintaan_produksi' => 'required|exists:permintaan_produksi,id_permintaan_produksi',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|string',
            'items.*.jumlah_pakai' => 'required|numeric|min:0.01',
        ], [
            'id_permintaan_produksi.required' => 'Job Order harus dipilih',
            'items.required' => 'Minimal harus ada 1 bahan baku',
            'items.min' => 'Minimal harus ada 1 bahan baku',
            'items.*.id_bahan.required' => 'Bahan Baku / WIP harus dipilih',
            'items.*.jumlah_pakai.required' => 'Jumlah Pakai harus diisi',
            'items.*.jumlah_pakai.min' => 'Jumlah Pakai minimal 0.01',
        ]);

        DB::beginTransaction();
        try {
            $processedItems = [];
            $grandTotalBiaya = 0;

            $job = PermintaanProduksi::findOrFail($request->id_permintaan_produksi);
            $accountingService = app(\App\Services\AccountingService::class);

            // Loop setiap item yang diinput
            foreach ($request->items as $index => $item) {
                // Skip jika bahan atau jumlah kosong
                if (empty($item['id_bahan']) || empty($item['jumlah_pakai'])) {
                    continue;
                }

                $prefixedId = $item['id_bahan'];
                $jumlahDibutuhkan = $item['jumlah_pakai'];

                $parts = explode('_', $prefixedId);
                if (count($parts) !== 2) {
                    throw new \Exception("Format ID bahan tidak valid: {$prefixedId}");
                }
                $type = $parts[0];
                $idVal = $parts[1];

                if ($type === 'bahan') {
                    $bahan = BahanBaku::findOrFail($idVal);

                    // Cek apakah stok mencukupi
                    if ($bahan->stok_saat_ini < $jumlahDibutuhkan) {
                        DB::rollback();
                        return back()->withErrors([
                            'items' => "Stok {$bahan->nama_bahan} tidak mencukupi! Stok tersedia: {$bahan->stok_saat_ini} {$bahan->satuan}"
                        ])->withInput();
                    }

                    // FIFO LOGIC: Ambil stok dari yang paling lama
                    $stokTersedia = StokBahanBaku::where('id_bahan', $idVal)
                                                ->where('status', 'tersedia')
                                                ->where('sisa_stok', '>', 0)
                                                ->orderBy('tanggal_masuk', 'asc')
                                                ->orderBy('id_stok', 'asc')
                                                ->get();

                    $sisaKebutuhan = $jumlahDibutuhkan;
                    $totalBiayaItem = 0;
                    $batches = [];

                    foreach ($stokTersedia as $stok) {
                        if ($sisaKebutuhan <= 0) break;

                        // Tentukan jumlah yang diambil dari batch ini
                        $jumlahAmbil = min($sisaKebutuhan, $stok->sisa_stok);
                        
                        // Hitung biaya untuk batch ini
                        $biayaBatch = $jumlahAmbil * $stok->harga_per_satuan;
                        $totalBiayaItem += $biayaBatch;

                        // Catat pemakaian
                        $pemakaian = PemakaianBahanBaku::create([
                            'id_permintaan_produksi' => $request->id_permintaan_produksi,
                            'id_bahan' => $idVal,
                            'id_produk_wip' => null,
                            'id_stok' => $stok->id_stok,
                            'id_stok_produk' => null,
                            'id_admin' => Auth::guard('admin')->id(),
                            'tanggal_pemakaian' => now(),
                            'jumlah_pakai' => $jumlahAmbil,
                            'harga_satuan' => $stok->harga_per_satuan,
                            'total_biaya' => $biayaBatch,
                            'keterangan' => $item['keterangan'] ?? null,
                        ]);

                        // JURNAL OTOMATIS: Pemakaian Bahan Baku
                        $accountingService->recordPemakaianBahanBaku($pemakaian, $job);

                        $batches[] = [
                            'id_stok' => $stok->id_stok,
                            'jumlah' => $jumlahAmbil,
                            'harga' => $stok->harga_per_satuan,
                            'biaya' => $biayaBatch,
                        ];

                        // Update sisa stok di batch ini
                        $stok->sisa_stok -= $jumlahAmbil;
                        if ($stok->sisa_stok <= 0) {
                            $stok->status = 'habis';
                        }
                        $stok->save();

                        // Kurangi sisa kebutuhan
                        $sisaKebutuhan -= $jumlahAmbil;
                    }

                    // Update stok_saat_ini di tabel bahan_baku
                    $bahan->stok_saat_ini -= $jumlahDibutuhkan;
                    $bahan->save();

                    $grandTotalBiaya += $totalBiayaItem;

                    $processedItems[] = [
                        'nama_bahan' => $bahan->nama_bahan,
                        'jumlah' => $jumlahDibutuhkan,
                        'satuan' => $bahan->satuan,
                        'total_biaya' => $totalBiayaItem,
                        'batches' => $batches,
                    ];
                } elseif ($type === 'wip') {
                    $wip = Produk::findOrFail($idVal);

                    // Get current stock of WIP
                    $stokWipTotal = \App\Models\StokProduk::where('id_produk', $idVal)
                        ->where('status', 'tersedia')
                        ->sum('sisa_stok');

                    // Cek apakah stok mencukupi
                    if ($stokWipTotal < $jumlahDibutuhkan) {
                        DB::rollback();
                        return back()->withErrors([
                            'items' => "Stok WIP {$wip->nama_produk} tidak mencukupi! Stok tersedia: {$stokWipTotal} {$wip->satuan_produk}"
                        ])->withInput();
                    }

                    // FIFO LOGIC for WIP: Ambil stok dari yang paling lama
                    $stokTersedia = \App\Models\StokProduk::where('id_produk', $idVal)
                                                ->where('status', 'tersedia')
                                                ->where('sisa_stok', '>', 0)
                                                ->orderBy('tanggal_masuk', 'asc')
                                                ->orderBy('id_stok_produk', 'asc')
                                                ->get();

                    $sisaKebutuhan = $jumlahDibutuhkan;
                    $totalBiayaItem = 0;
                    $batches = [];

                    foreach ($stokTersedia as $stok) {
                        if ($sisaKebutuhan <= 0) break;

                        // Tentukan jumlah yang diambil dari batch ini
                        $jumlahAmbil = min($sisaKebutuhan, $stok->sisa_stok);
                        
                        // Hitung biaya untuk batch ini
                        $biayaBatch = $jumlahAmbil * $stok->harga_pokok_per_unit;
                        $totalBiayaItem += $biayaBatch;

                        // Catat pemakaian
                        $pemakaian = PemakaianBahanBaku::create([
                            'id_permintaan_produksi' => $request->id_permintaan_produksi,
                            'id_bahan' => null,
                            'id_produk_wip' => $idVal,
                            'id_stok' => null,
                            'id_stok_produk' => $stok->id_stok_produk,
                            'id_admin' => Auth::guard('admin')->id(),
                            'tanggal_pemakaian' => now(),
                            'jumlah_pakai' => $jumlahAmbil,
                            'harga_satuan' => $stok->harga_pokok_per_unit,
                            'total_biaya' => $biayaBatch,
                            'keterangan' => $item['keterangan'] ?? null,
                        ]);

                        // JURNAL OTOMATIS: Pemakaian Bahan Baku (WIP)
                        $accountingService->recordPemakaianBahanBaku($pemakaian, $job);

                        $batches[] = [
                            'id_stok' => $stok->id_stok_produk,
                            'jumlah' => $jumlahAmbil,
                            'harga' => $stok->harga_pokok_per_unit,
                            'biaya' => $biayaBatch,
                        ];

                        // Update sisa stok di batch ini
                        $stok->sisa_stok -= $jumlahAmbil;
                        if ($stok->sisa_stok <= 0) {
                            $stok->status = 'habis';
                        }
                        $stok->save();

                        // Kurangi sisa kebutuhan
                        $sisaKebutuhan -= $jumlahAmbil;
                    }

                    $grandTotalBiaya += $totalBiayaItem;

                    $processedItems[] = [
                        'nama_bahan' => $wip->nama_produk,
                        'jumlah' => $jumlahDibutuhkan,
                        'satuan' => $wip->satuan_produk,
                        'total_biaya' => $totalBiayaItem,
                        'batches' => $batches,
                    ];
                }
            }

            // Validasi: Apakah ada item yang berhasil diproses?
            if (count($processedItems) === 0) {
                DB::rollback();
                return back()->withErrors([
                    'items' => 'Tidak ada bahan atau WIP yang valid untuk diproses!'
                ])->withInput();
            }

            // Recalculate total biaya produksi di job order
            $biayaBreakdown = $job->hitungTotalBiayaProduksi();
            
            DB::commit();

            return redirect()->route('pemakaian-bahan-baku.index')
                           ->with('success', 'Berhasil menyimpan ' . count($processedItems) . ' pemakaian bahan/WIP dengan metode FIFO!')
                           ->with('processed_items', $processedItems)
                           ->with('total_biaya', $grandTotalBiaya);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error saving pemakaian bahan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $pemakaian = PemakaianBahanBaku::findOrFail($id);

        // Tidak bisa delete kalau job sudah selesai
        if ($pemakaian->permintaanProduksi->status == 'selesai') {
            return back()->with('error', 'Tidak dapat menghapus pemakaian dari job yang sudah selesai!');
        }

        DB::beginTransaction();
        try {
            // Cari dan hapus jurnal umum lama beserta detailnya
            $oldJurnals = JurnalUmum::where('id_referensi', $pemakaian->id_pemakaian)
                                    ->where('tipe_referensi', 'pemakaian_bahan_baku')
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

            if ($pemakaian->id_bahan) {
                // Kembalikan stok ke batch FIFO bahan baku
                $stok = $pemakaian->stokBahanBaku;
                if ($stok) {
                    $stok->sisa_stok += $pemakaian->jumlah_pakai;
                    $stok->status = 'tersedia';
                    $stok->save();
                }

                // Update stok bahan baku
                $bahan = $pemakaian->bahanBaku;
                $bahan->stok_saat_ini += $pemakaian->jumlah_pakai;
                $bahan->save();
            } elseif ($pemakaian->id_produk_wip) {
                // Kembalikan stok ke batch FIFO WIP
                $stok = $pemakaian->stokProduk;
                if ($stok) {
                    $stok->sisa_stok += $pemakaian->jumlah_pakai;
                    $stok->status = 'tersedia';
                    $stok->save();
                }
            }

            // Update biaya di job order
            $job = $pemakaian->permintaanProduksi;
            
            // Delete pemakaian
            $pemakaian->delete();
            
            // Recalculate job setelah delete
            $biayaBreakdown = $job->hitungTotalBiayaProduksi();

            // Update saldo akun berjalan
            $akuns = Akun::all();
            foreach ($akuns as $akun) {
                $akun->saldo = $akun->hitungSaldo();
                $akun->save();
            }
            
            DB::commit();

            return redirect()->route('pemakaian-bahan-baku.index')
                           ->with('success', 'Pemakaian bahan/WIP berhasil dihapus dan stok dikembalikan!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error deleting pemakaian bahan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}