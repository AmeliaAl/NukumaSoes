<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PersediaanEntry;
use App\Models\ProdukKeluarEntry;
use App\Models\KartuStokEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $entries = ProdukKeluarEntry::when($search, function ($query) use ($search) {
            return $query->where('kode_produk', 'like', '%' . $search . '%')
                         ->orWhere('nama_produk', 'like', '%' . $search . '%');
        })
        ->orderBy('tanggal', 'desc')
        ->paginate(10);

        // Fetch all active batches from Inventory
        $inventoriesForSelect = \App\Models\Inventory::where('jumlah', '>', 0)
            ->where('status', '!=', 'Expired')
            ->orderByRaw('tgl_expired IS NULL, tgl_expired ASC')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->map(function($item) {
                // Get selling price and HPP from product master
                $mainProduct = \App\Models\Product::where('kode_produk', $item->kode_produk)->first();
                $sellingPrice = $mainProduct ? $mainProduct->harga : 0;
                $hpp = $mainProduct ? $mainProduct->hpp : 0;

                $item->selling_price = $sellingPrice;
                $item->hpp_master = $hpp; // to avoid conflict with inventory's own hpp if any
                return $item;
            });

        $categories = \App\Models\Category::all();
        
        $categoryPrices = collect([]); // No longer used, handled by JS from inventories data

        $flavors = \App\Models\Flavor::all();
        
        $allProductsData = \App\Models\Product::select('kategori', 'rasa_produk', 'nama_produk', 'kode_produk', 'hpp')->get();
        
        return view('produk-keluar.index', compact('entries', 'search', 'inventoriesForSelect', 'categories', 'flavors', 'categoryPrices', 'allProductsData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch Inventory records for the selection dropdown (Detail Persediaan Produk)
        $inventoriesForSelect = \App\Models\Inventory::where('jumlah', '>', 0)
            ->where('status', '!=', 'Expired')
            ->orderByRaw('tgl_expired IS NULL, tgl_expired ASC')
            ->orderBy('created_at', 'ASC')
            ->get();

        return view('produk-keluar.create', compact('inventoriesForSelect'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Strip 'Rp ' from harga and total_harga if present
        $harga = str_replace(['Rp ', '.', ','], '', $request->harga);
        $total_harga = str_replace(['Rp ', '.', ','], '', $request->total_harga);
        $request->merge([
            'harga' => $harga,
            'total_harga' => $total_harga
        ]);

        $request->validate([
            'id_transaksi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'kode_produk' => 'required|string|max:255',
            'inventory_id' => 'nullable|exists:inventories,id',
            'nama_produk' => 'nullable|string|max:255',
            'jumlah_keluar' => 'required|integer|min:1',
            'harga' => 'nullable|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'jumlah_pack_keluar' => 'nullable|integer|min:0',
            'harga_pokok_per_pack' => 'nullable|numeric|min:0',
            'harga_persediaan_produk_jadi' => 'nullable|numeric|min:0',
            'jumlah_pack' => 'nullable|integer|min:0',
        ]);

        $request->merge(['status' => 'lunas']);

        $totalAvailableAll = \App\Models\Inventory::where('kode_produk', $request->kode_produk)
            ->where('status', '!=', 'Expired')
            ->sum('jumlah');
        
        if ($totalAvailableAll < $request->jumlah_keluar) {
            return redirect()->back()->withErrors(['jumlah_keluar' => 'Total keseluruhan stok produk ini tidak mencukupi (Tersedia Keseluruhan: ' . $totalAvailableAll . ').']);
        }

        DB::transaction(function () use ($request) {
            $remainingToDeduct = $request->jumlah_keluar;
            
            // Prioritize the selected batch first
            $selectedBatch = \App\Models\Inventory::where('id', $request->inventory_id)
                ->where('jumlah', '>', 0)
                ->where('status', '!=', 'Expired')
                ->first();
                
            $batchesToDeduct = collect();
            
            if ($selectedBatch) {
                $batchesToDeduct->push($selectedBatch);
            }
            
            // Get other batches ordered by FEFO (tgl_expired ASC)
            $otherBatches = \App\Models\Inventory::where('kode_produk', $request->kode_produk)
                ->where('id', '!=', $request->inventory_id)
                ->where('jumlah', '>', 0)
                ->where('status', '!=', 'Expired')
                ->orderByRaw('tgl_expired IS NULL, tgl_expired ASC')
                ->orderBy('created_at', 'ASC')
                ->get();
                
            $batchesToDeduct = $batchesToDeduct->concat($otherBatches);
            
            $mainProduct = \App\Models\Product::where('kode_produk', $request->kode_produk)->first();
            $request->merge([
                'kategori' => $mainProduct->kategori ?? null,
                'jenis' => 'keluar'
            ]);

            foreach ($batchesToDeduct as $batch) {
                if ($remainingToDeduct <= 0) break;

                $deductFromThisBatch = min($remainingToDeduct, $batch->jumlah);
                $batch->jumlah -= $deductFromThisBatch;
                // Jika stok saat ini sudah habis, stok_awal juga dikosongkan
                if ($batch->jumlah <= 0) {
                    $batch->jumlah = 0;
                    $batch->stok_awal = 0;
                }
                $batch->save();

                $remainingToDeduct -= $deductFromThisBatch;

                $proporsi = $deductFromThisBatch / $request->jumlah_keluar;
                $totalHargaProporsional = $proporsi * $request->total_harga;
                $hppProporsional = $batch->hpp ?? $request->harga_pokok_per_pack ?? 0;

                KartuStokEntry::create([
                    'tanggal' => $request->tanggal,
                    'keterangan' => 'Produk Keluar (Batch: ' . ($batch->no_batch ?? '-') . ')',
                    'id_transaksi' => $request->id_transaksi,
                    'masuk' => 0,
                    'keluar' => $deductFromThisBatch,
                    'harga' => $request->harga,
                    'total_harga' => $totalHargaProporsional,
                    'kode_produk' => $batch->kode_produk,
                    'nama_produk' => $batch->nama_produk,
                    'no_batch' => $batch->no_batch,
                ]);

                // Create ProdukKeluarEntry for this specific batch
                $entryData = $request->all();
                $entryData['inventory_id'] = $batch->id;
                $entryData['jumlah_keluar'] = $deductFromThisBatch;
                $entryData['jumlah_pack_keluar'] = $deductFromThisBatch;
                $entryData['total_harga'] = $totalHargaProporsional;
                $entryData['harga_pokok_per_pack'] = $hppProporsional;
                \App\Models\ProdukKeluarEntry::create($entryData);
            }
            \App\Models\Product::syncQuantity($request->kode_produk);

            // Jurnal Umum creation has been removed per user request
        });

        return redirect()->route('produk-keluar.index')->with('success', 'Entry berhasil dibuat. Stok dikurangi menggunakan sistem FEFO otomatis.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $entry = ProdukKeluarEntry::findOrFail($id);
        return view('produk-keluar.show', compact('entry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $entry = ProdukKeluarEntry::findOrFail($id);
        $products = Product::where('tgl_expired', '>', date('Y-m-d'))->orderBy('tgl_expired', 'asc')->get();
        return view('produk-keluar.edit', compact('entry', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $entry = ProdukKeluarEntry::findOrFail($id);

        // Strip formatting from currency fields
        $total_harga = str_replace(['Rp ', '.', ','], '', $request->total_harga);
        $harga_persediaan = str_replace(['Rp ', '.', ','], '', $request->harga_persediaan_produk_jadi);
        
        $request->merge([
            'total_harga' => $total_harga,
            'harga_persediaan_produk_jadi' => $harga_persediaan,
        ]);

        $request->validate([
            'tanggal' => 'required|date',
            'jumlah_keluar' => 'required|integer|min:1',
            'total_harga' => 'required|numeric|min:0',
            'jumlah_pack' => 'nullable|integer|min:0',
            'harga_persediaan_produk_jadi' => 'nullable|numeric|min:0',
            'harga_pokok_per_pack' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $entry) {
            // 1. Restore old stock to batches if we have record of where it came from
            if ($entry->inventory_id) {
                $oldInventory = \App\Models\Inventory::find($entry->inventory_id);
                if ($oldInventory) {
                    $oldInventory->jumlah += $entry->jumlah_keluar;
                    $oldInventory->save();
                }
            }

            // 2. Update the entry with new data
            $entry->update([
                'tanggal' => $request->tanggal,
                'jumlah_keluar' => $request->jumlah_keluar,
                'jumlah_pack_keluar' => $request->jumlah_keluar,
                'total_harga' => $request->total_harga,
                'jumlah_pack' => $request->jumlah_pack,
                'harga_persediaan_produk_jadi' => $request->harga_persediaan_produk_jadi,
                'harga_pokok_per_pack' => $request->harga_pokok_per_pack,
            ]);

            // 3. Re-deduct stock based on new quantity (FEFO)
            $remainingToDeduct = $request->jumlah_keluar;
            $batches = \App\Models\Inventory::where('kode_produk', $entry->kode_produk)
                ->where('jumlah', '>', 0)
                ->where('status', '!=', 'Expired')
                ->orderByRaw('tgl_expired IS NULL, tgl_expired ASC')
                ->get();

            // Delete old KartuStokEntry
            KartuStokEntry::where('id_transaksi', $entry->id_transaksi)->delete();

            foreach ($batches as $batch) {
                if ($remainingToDeduct <= 0) break;

                $deductAmount = min($batch->jumlah, $remainingToDeduct);
                $batch->jumlah -= $deductAmount;
                // Jika stok saat ini sudah habis, stok_awal juga dikosongkan
                if ($batch->jumlah <= 0) {
                    $batch->jumlah = 0;
                    $batch->stok_awal = 0;
                }
                $batch->save();
                
                // Recreate KartuStokEntry
                KartuStokEntry::create([
                    'tanggal' => $request->tanggal,
                    'keterangan' => 'Produk Keluar (Batch: ' . ($batch->no_batch ?? '-') . ')',
                    'id_transaksi' => $entry->id_transaksi,
                    'masuk' => 0,
                    'keluar' => $deductAmount,
                    'harga' => $request->harga_pokok_per_pack ?? 0,
                    'total_harga' => ($deductAmount / $request->jumlah_keluar) * $request->total_harga,
                    'kode_produk' => $batch->kode_produk,
                    'nama_produk' => $batch->nama_produk,
                    'no_batch' => $batch->no_batch,
                ]);

                $remainingToDeduct -= $deductAmount;
            }
            
            \App\Models\Product::syncQuantity($entry->kode_produk);

            // 4. Update Jurnal Umum (Delete old and create new)
            \App\Models\JurnalUmum::where('id_transaksi', $entry->id_transaksi)->delete();

            // Recording to Jurnal Umum has been removed per user request
        });

        return redirect()->route('produk-keluar.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entry = ProdukKeluarEntry::findOrFail($id);
        
        DB::transaction(function () use ($entry) {
            // Restore stock to inventory batch
            if ($entry->inventory_id) {
                $inventory = \App\Models\Inventory::find($entry->inventory_id);
                if ($inventory) {
                    $inventory->jumlah += $entry->jumlah_keluar;
                    $inventory->save();
                }
            }
            
            $kode_produk = $entry->kode_produk;
            $entry->delete();
            
            // Sync total product quantity
            Product::syncQuantity($kode_produk);

            // Delete related Jurnal Umum and Kartu Stok
            \App\Models\JurnalUmum::where('id_transaksi', $entry->id_transaksi)->delete();
            KartuStokEntry::where('id_transaksi', $entry->id_transaksi)->delete();
        });

        return redirect()->route('produk-keluar.index')->with('success', 'Entry deleted successfully and stock restored.');
    }
}
