<?php

namespace App\Http\Controllers;

use App\Models\KartuStokEntry;
use App\Models\PersediaanEntry;
use App\Models\ProdukKeluarEntry;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class KartuStokController extends Controller
{
    public function index(Request $request) 
    {
        $data = $this->getData($request);
        return view('kartu-stok.index', $data);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getData($request);
        $pdf = Pdf::loadView('kartu-stok.pdf', $data);
        return $pdf->setPaper('a4', 'landscape')->download('kartu-stok.pdf');
    }

    private function getData(Request $request)
    {
        $kodeProduk = $request->get('kode_produk');
        $kategori   = $request->get('kategori');
        $periode    = $request->get('periode', date('Y-m'));

        $selectedProduct = null;
        if ($kodeProduk) {
            $selectedProduct = Product::where('kode_produk', $kodeProduk)->first();
            if (!$selectedProduct) {
                $inv = Inventory::where('kode_produk', $kodeProduk)->first();
                if ($inv) {
                    $selectedProduct = (object)['nama_produk' => $inv->nama_produk, 'kode_produk' => $inv->kode_produk, 'satuan' => 'Unit'];
                }
            }
            if (!$selectedProduct) {
                $entry = PersediaanEntry::where('kode_produk', $kodeProduk)->first();
                if ($entry) {
                    $selectedProduct = (object)['nama_produk' => $entry->nama_produk, 'kode_produk' => $entry->kode_produk, 'satuan' => 'Unit'];
                }
            }
        }

        // ─── 1. Kumpulkan semua transaksi mentah (masuk + keluar + expired) ───────

        // Produk Masuk
        $masukRaw = PersediaanEntry::when($kodeProduk, fn($q) => $q->where('kode_produk', $kodeProduk))
            ->when($kategori, fn($q) => $q->whereHas('inventory', fn($sq) => $sq->where('kategori', $kategori)))
            ->orderBy('tanggal')->orderBy('created_at')->orderBy('id')
            ->get()
            ->map(function ($entry) {
                $inv = $entry->inventory_id ? Inventory::find($entry->inventory_id) : null;
                if (!$inv && !empty($entry->no_batch)) {
                    $inv = Inventory::where('kode_produk', $entry->kode_produk)->where('no_batch', $entry->no_batch)->first();
                }
                return [
                    'sort_tanggal'   => $entry->tanggal,
                    'sort_created'   => optional($entry->created_at)->toDateTimeString(),
                    'sort_id'        => $entry->id,
                    'type'           => 'masuk',
                    'tanggal'        => $entry->tanggal,
                    'id_transaksi'   => $entry->id_transaksi,
                    'kode_produk'    => $entry->kode_produk,
                    'nama_produk'    => $entry->nama_produk,
                    'no_batch'       => $entry->no_batch ?? '',
                    'qty'            => (int)($entry->jumlah_masuk ?? 0) + (int)($entry->stok_awal ?? 0),
                    'hpp'            => $inv ? (float)$inv->hpp : 0.0,
                    'tgl_masuk'      => $inv ? $inv->tgl_masuk : null,
                    'tgl_expired'    => $inv ? $inv->tgl_expired : null,
                ];
            });

        // Produk Keluar — digroup per id_transaksi agar satu transaksi multi-batch tampil sebagai satu blok
        $keluarRaw = ProdukKeluarEntry::when($kodeProduk, fn($q) => $q->where('kode_produk', $kodeProduk))
            ->when($kategori, fn($q) => $q->where(fn($q2) => $q2->where('kategori', $kategori)
                ->orWhereHas('inventory', fn($sq) => $sq->where('kategori', $kategori))))
            ->orderBy('tanggal')->orderBy('created_at')->orderBy('id')
            ->get()
            ->groupBy('id_transaksi')
            ->map(function ($group) {
                // Gunakan entry pertama untuk metadata transaksi
                $first   = $group->first();
                $batches = $group->map(function ($entry) {
                    $inv = $entry->inventory_id ? Inventory::find($entry->inventory_id) : null;
                    return [
                        'no_batch'    => $entry->no_batch ?? ($inv ? $inv->no_batch : ''),
                        'qty'         => (int)$entry->jumlah_keluar,
                        'hpp'         => (float)($entry->harga_pokok_per_pack ?? ($inv ? $inv->hpp : 0)),
                        'tgl_masuk'   => $inv ? $inv->tgl_masuk : null,
                        'tgl_expired' => $inv ? $inv->tgl_expired : null,
                    ];
                })->values()->all();

                return [
                    'sort_tanggal'   => $first->tanggal,
                    'sort_created'   => optional($first->created_at)->toDateTimeString(),
                    'sort_id'        => $first->id + 500000,
                    'type'           => 'keluar',
                    'tanggal'        => $first->tanggal,
                    'id_transaksi'   => $first->id_transaksi,
                    'kode_produk'    => $first->kode_produk,
                    'nama_produk'    => $first->nama_produk,
                    'batches'        => $batches, // array per-batch
                    // field qty/no_batch/dll di-resolve saat proses rows
                ];
            })
            ->values();

        // Keluar karena Expired (dari KartuStokEntry dengan prefix EXP-)
        $expiredRaw = KartuStokEntry::where('keluar', '>', 0)
            ->where('id_transaksi', 'like', 'EXP-%')
            ->when($kodeProduk, fn($q) => $q->where('kode_produk', $kodeProduk))
            ->orderBy('tanggal')->orderBy('created_at')->orderBy('id')
            ->get()
            ->map(function ($entry) {
                $inv = (!empty($entry->no_batch) && !empty($entry->kode_produk))
                    ? Inventory::where('kode_produk', $entry->kode_produk)->where('no_batch', $entry->no_batch)->first()
                    : null;
                $expHist = !$inv
                    ? \App\Models\ExpiredProductHistory::where('no_batch', $entry->no_batch)->where('kode_produk', $entry->kode_produk)->first()
                    : null;
                $tglExp  = $inv ? $inv->tgl_expired : ($expHist ? $expHist->tgl_expired : null);
                $hpp     = $entry->harga > 0 ? (float)$entry->harga : (float)($inv ? $inv->hpp : ($expHist ? $expHist->hpp : 0));
                return [
                    'sort_tanggal'   => $entry->tanggal,
                    'sort_created'   => optional($entry->created_at)->toDateTimeString(),
                    'sort_id'        => $entry->id + 900000,
                    'type'           => 'keluar',
                    'tanggal'        => $entry->tanggal,
                    'id_transaksi'   => $entry->id_transaksi,
                    'kode_produk'    => $entry->kode_produk,
                    'nama_produk'    => $entry->nama_produk,
                    'batches'        => [[
                        'no_batch'    => $entry->no_batch ?? '',
                        'qty'         => (int)$entry->keluar,
                        'hpp'         => $hpp,
                        'tgl_masuk'   => $inv ? $inv->tgl_masuk : ($expHist ? $expHist->tgl_masuk : null),
                        'tgl_expired' => $tglExp,
                    ]],
                ];
            });

        // Gabung dan urutkan semua transaksi
        $allTransactions = collect($masukRaw)
            ->concat($keluarRaw)
            ->concat($expiredRaw)
            ->sortBy([
                ['sort_tanggal', 'asc'],
                ['sort_created', 'asc'],
                ['sort_id', 'asc'],
            ])
            ->values();

        // ─── 2. State awal batch dari transaksi sebelum periode ──────────────────
        //
        // batchState: keyed by no_batch → ['qty', 'hpp', 'tgl_masuk', 'tgl_expired', 'tgl_expired_raw']
        $batchState = [];

        $periodStart = $periode ? ($periode . '-01') : null;

        if ($periodStart) {
            // Masuk sebelum periode
            $preMasuk = PersediaanEntry::where('tanggal', '<', $periodStart)
                ->when($kodeProduk, fn($q) => $q->where('kode_produk', $kodeProduk))
                ->orderBy('tanggal')->orderBy('created_at')->orderBy('id')
                ->get();
            foreach ($preMasuk as $m) {
                $batchKey = !empty($m->no_batch) ? $m->no_batch : 'unknown';
                $inv = $m->inventory_id ? Inventory::find($m->inventory_id) : null;
                if (!$inv && !empty($m->no_batch)) {
                    $inv = Inventory::where('kode_produk', $m->kode_produk)->where('no_batch', $m->no_batch)->first();
                }
                if (!isset($batchState[$batchKey])) {
                    $batchState[$batchKey] = [
                        'qty'           => 0,
                        'hpp'           => $inv ? (float)$inv->hpp : 0.0,
                        'tgl_masuk'     => $inv ? $inv->tgl_masuk : null,
                        'tgl_expired'   => $inv ? ($inv->tgl_expired ? \Carbon\Carbon::parse($inv->tgl_expired)->format('d/m/Y') : null) : null,
                        'tgl_expired_raw' => $inv ? $inv->tgl_expired : null,
                    ];
                }
                $batchState[$batchKey]['qty'] += (int)($m->jumlah_masuk ?? 0) + (int)($m->stok_awal ?? 0);
            }

            // Keluar sebelum periode
            $preKeluar = ProdukKeluarEntry::where('tanggal', '<', $periodStart)
                ->when($kodeProduk, fn($q) => $q->where('kode_produk', $kodeProduk))
                ->orderBy('tanggal')->orderBy('created_at')->orderBy('id')
                ->get();
            foreach ($preKeluar as $k) {
                $inv = $k->inventory_id ? Inventory::find($k->inventory_id) : null;
                $batchKey = !empty($k->no_batch) ? $k->no_batch : ($inv && !empty($inv->no_batch) ? $inv->no_batch : 'unknown');
                if (isset($batchState[$batchKey])) {
                    $batchState[$batchKey]['qty'] -= (int)$k->jumlah_keluar;
                    if ($batchState[$batchKey]['qty'] < 0) $batchState[$batchKey]['qty'] = 0;
                }
            }

            // Buang batch dengan qty 0 dari saldo awal (sudah habis)
            $batchState = array_filter($batchState, fn($b) => $b['qty'] > 0);
        }

        // ─── 3. Filter hanya transaksi dalam periode ─────────────────────────────
        if ($periode) {
            $allTransactions = $allTransactions->filter(function ($tx) use ($periode) {
                return str_starts_with($tx['sort_tanggal'], $periode);
            })->values();
        }

        // ─── 4. Proses setiap transaksi → hasilkan baris-baris FEFO ─────────────
        //
        // Setiap transaksi menghasilkan array 'rows':
        //   row utama  → data header transaksi (tanggal, no_batch, deskripsi, dll.)
        //   sub-baris  → saldo per-batch setelah transaksi
        //
        // Format baris:
        //   is_header   : bool  – apakah baris header transaksi
        //   tanggal     : string
        //   no_batch    : string
        //   keterangan  : string
        //   tgl_masuk   : string|null
        //   tgl_expired : string|null
        //   tgl_expired_raw : date|null  (untuk styling)
        //   masuk       : int|null
        //   keluar      : int|null
        //   sisa        : int
        //   hpp         : float|null
        //   nilai_persediaan : float

        $rows = [];
        $totalMasuk  = 0;
        $totalKeluar = 0;
        $totalNilaiMasuk  = 0;
        $totalNilaiKeluar = 0;

        foreach ($allTransactions as $tx) {
            $batchKey = !empty($tx['no_batch']) ? $tx['no_batch'] : 'unknown';

            if ($tx['type'] === 'masuk') {
                // ── Update state ──
                if (!isset($batchState[$batchKey])) {
                    $batchState[$batchKey] = [
                        'qty'             => 0,
                        'hpp'             => $tx['hpp'],
                        'tgl_masuk'       => $tx['tgl_masuk'],
                        'tgl_expired'     => $tx['tgl_expired'] ? \Carbon\Carbon::parse($tx['tgl_expired'])->format('d/m/Y') : null,
                        'tgl_expired_raw' => $tx['tgl_expired'],
                    ];
                }
                $batchState[$batchKey]['qty'] += $tx['qty'];
                if ($tx['hpp'] > 0)     $batchState[$batchKey]['hpp']             = $tx['hpp'];
                if ($tx['tgl_masuk'])   $batchState[$batchKey]['tgl_masuk']       = $tx['tgl_masuk'];
                if ($tx['tgl_expired']) {
                    $batchState[$batchKey]['tgl_expired']     = \Carbon\Carbon::parse($tx['tgl_expired'])->format('d/m/Y');
                    $batchState[$batchKey]['tgl_expired_raw'] = $tx['tgl_expired'];
                }

                $totalMasuk      += $tx['qty'];
                $totalNilaiMasuk += $tx['qty'] * $tx['hpp'];

                // ── Baris header masuk ──
                $rows[] = [
                    'is_header'       => true,
                    'is_pre_sub'      => false,
                    'type'            => 'masuk',
                    'tanggal'         => $tx['tanggal'],
                    'no_batch'        => $tx['no_batch'],
                    'keterangan'      => 'Produk Masuk',
                    'tgl_masuk'       => $batchState[$batchKey]['tgl_masuk'],
                    'tgl_expired'     => $batchState[$batchKey]['tgl_expired'],
                    'tgl_expired_raw' => $batchState[$batchKey]['tgl_expired_raw'],
                    'masuk'           => $tx['qty'],
                    'keluar'          => null,
                    'sisa'            => $batchState[$batchKey]['qty'],
                    'hpp'             => $batchState[$batchKey]['hpp'],
                    'nilai_persediaan'=> $batchState[$batchKey]['qty'] * $batchState[$batchKey]['hpp'],
                ];

                // ── Sub-baris: saldo semua batch SETELAH transaksi (FEFO), kecuali batch yang baru masuk ──
                $allBatchesAfter = $this->sortBatchesByFefo(
                    array_filter($batchState, fn($b, $k) => $k !== $batchKey && $b['qty'] > 0, ARRAY_FILTER_USE_BOTH)
                );
                foreach ($allBatchesAfter as $afterKey => $afterBatch) {
                    $rows[] = [
                        'is_header'       => false,
                        'is_pre_sub'      => false,
                        'type'            => 'masuk',
                        'tanggal'         => null,
                        'no_batch'        => $afterKey !== 'unknown' ? $afterKey : '-',
                        'keterangan'      => null,
                        'tgl_masuk'       => $afterBatch['tgl_masuk'],
                        'tgl_expired'     => $afterBatch['tgl_expired'],
                        'tgl_expired_raw' => $afterBatch['tgl_expired_raw'],
                        'masuk'           => null,
                        'keluar'          => null,
                        'sisa'            => $afterBatch['qty'],
                        'hpp'             => $afterBatch['hpp'],
                        'nilai_persediaan'=> $afterBatch['qty'] * $afterBatch['hpp'],
                    ];
                }

            } else {
                // type === 'keluar'
                $batches  = $tx['batches'] ?? [];
                $totalQty = array_sum(array_column($batches, 'qty'));
                $isExpTx  = str_starts_with((string)$tx['id_transaksi'], 'EXP-');

                // Batch pertama (FEFO) sebagai representasi header
                $firstBatch        = $batches[0] ?? [];
                $firstKey          = !empty($firstBatch['no_batch']) ? $firstBatch['no_batch'] : 'unknown';
                $lastBatchTglMasuk = $firstBatch['tgl_masuk'] ?? null;
                $lastBatchTglExp   = $firstBatch['tgl_expired'] ?? null;

                // ── Kurangi stok & hitung HPP tertimbang ──
                $subtotalHpp = 0.0;
                foreach ($batches as $b) {
                    $bk = !empty($b['no_batch']) ? $b['no_batch'] : 'unknown';
                    if (isset($batchState[$bk])) {
                        $batchState[$bk]['qty'] -= $b['qty'];
                        if ($batchState[$bk]['qty'] < 0) $batchState[$bk]['qty'] = 0;
                    }
                    $subtotalHpp      += $b['qty'] * $b['hpp'];
                    $lastBatchTglMasuk = $b['tgl_masuk'] ?? $lastBatchTglMasuk;
                    $lastBatchTglExp   = $b['tgl_expired'] ?? $lastBatchTglExp;
                }

                $hppRata = $totalQty > 0 ? round($subtotalHpp / $totalQty, 2) : 0.0;
                $totalKeluar      += $totalQty;
                $totalNilaiKeluar += $subtotalHpp;

                // Sisa & HPP header: ambil dari batch pertama yang masih ada setelah transaksi (FEFO)
                // Jika batch utama habis (0), ambil dari batch berikutnya yang masih ada
                $sortedStateAfter = $this->sortBatchesByFefo(
                    array_filter($batchState, fn($b) => $b['qty'] > 0)
                );
                $firstNonZero = reset($sortedStateAfter); // batch pertama yang qty > 0
                $sisaHeader   = $firstNonZero ? $firstNonZero['qty'] : 0;
                $hppHeader    = $firstNonZero ? $firstNonZero['hpp'] : $hppRata;

                // No batch: jika multi-batch tampilkan semua
                $batchNames   = array_unique(array_filter(array_column($batches, 'no_batch')));
                $displayBatch = count($batchNames) > 1
                    ? implode(', ', $batchNames)
                    : ($batchNames[0] ?? '-');

                // ── Baris header keluar ──
                $rows[] = [
                    'is_header'       => true,
                    'is_pre_sub'      => false,
                    'type'            => 'keluar',
                    'tanggal'         => $tx['tanggal'],
                    'no_batch'        => $displayBatch,
                    'keterangan'      => $isExpTx ? 'Produk Keluar (Expired)' : 'Produk Keluar',
                    'tgl_masuk'       => $lastBatchTglMasuk,
                    'tgl_expired'     => $lastBatchTglExp
                        ? \Carbon\Carbon::parse($lastBatchTglExp)->format('d/m/Y')
                        : null,
                    'tgl_expired_raw' => $lastBatchTglExp,
                    'masuk'           => null,
                    'keluar'          => $totalQty,
                    'sisa'            => $sisaHeader,
                    'hpp'             => $hppHeader,
                    'nilai_persediaan'=> $sisaHeader * $hppHeader,
                ];

                // ── Sub-baris: saldo semua batch SETELAH transaksi (FEFO) ──
                $allBatchesAfter = $this->sortBatchesByFefo(
                    array_filter($batchState, fn($b) => $b['qty'] > 0)
                );
                foreach ($allBatchesAfter as $afterKey => $afterBatch) {
                    $rows[] = [
                        'is_header'       => false,
                        'is_pre_sub'      => false,
                        'type'            => 'keluar',
                        'tanggal'         => null,
                        'no_batch'        => $afterKey !== 'unknown' ? $afterKey : '-',
                        'keterangan'      => null,
                        'tgl_masuk'       => $afterBatch['tgl_masuk'],
                        'tgl_expired'     => $afterBatch['tgl_expired'],
                        'tgl_expired_raw' => $afterBatch['tgl_expired_raw'],
                        'masuk'           => null,
                        'keluar'          => null,
                        'sisa'            => $afterBatch['qty'],
                        'hpp'             => $afterBatch['hpp'],
                        'nilai_persediaan'=> $afterBatch['qty'] * $afterBatch['hpp'],
                    ];
                }
            }
        }

        // ─── 5. Ringkasan per expired date (dari state akhir) ────────────────────
        $totalPerExpired = [];
        foreach ($batchState as $batchKey => $batch) {
            if ($batch['qty'] <= 0) continue;
            $expiredKey = $batch['tgl_expired'] ?? 'Tanpa Expired';
            if (!isset($totalPerExpired[$expiredKey])) {
                $totalPerExpired[$expiredKey] = ['sisa' => 0, 'hpp' => $batch['hpp'], 'nilai' => 0, 'tgl_expired_raw' => $batch['tgl_expired_raw']];
            }
            $totalPerExpired[$expiredKey]['sisa']  += $batch['qty'];
            $totalPerExpired[$expiredKey]['nilai']  = $totalPerExpired[$expiredKey]['sisa'] * $totalPerExpired[$expiredKey]['hpp'];
        }
        uksort($totalPerExpired, function ($a, $b) use ($totalPerExpired) {
            $rawA = $totalPerExpired[$a]['tgl_expired_raw'] ?? null;
            $rawB = $totalPerExpired[$b]['tgl_expired_raw'] ?? null;
            $tsA  = $rawA ? \Carbon\Carbon::parse($rawA)->timestamp : PHP_INT_MAX;
            $tsB  = $rawB ? \Carbon\Carbon::parse($rawB)->timestamp : PHP_INT_MAX;
            return $tsA <=> $tsB;
        });

        $totalNilaiBersih = $totalNilaiMasuk - $totalNilaiKeluar;
        $sisaAkhir        = array_sum(array_column($batchState, 'qty'));

        // Get all categories and inventories for filters
        $categories  = Category::all();
        $inventories = Inventory::select('kode_produk', 'nama_produk', 'no_batch')->distinct()->get();

        return compact(
            'rows', 'kodeProduk', 'categories', 'inventories', 'selectedProduct', 'periode',
            'totalNilaiMasuk', 'totalNilaiKeluar', 'totalMasuk', 'totalKeluar',
            'totalNilaiBersih', 'sisaAkhir', 'totalPerExpired'
        );
    }

    /**
     * Urutkan batch berdasarkan FEFO: tgl_expired paling dekat duluan, null paling akhir.
     */
    private function sortBatchesByFefo(array $batches): array
    {
        uasort($batches, function ($a, $b) {
            $rawA = $a['tgl_expired_raw'] ?? null;
            $rawB = $b['tgl_expired_raw'] ?? null;
            if ($rawA === null && $rawB === null) return 0;
            if ($rawA === null) return 1;
            if ($rawB === null) return -1;
            return \Carbon\Carbon::parse($rawA)->timestamp <=> \Carbon\Carbon::parse($rawB)->timestamp;
        });
        return $batches;
    }
}
