<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PersediaanEntry;
use App\Models\KartuStokEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersediaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $entries = PersediaanEntry::when($search, function ($query, $search) {
            return $query->where('kode_produk', 'like', '%' . $search . '%')
                         ->orWhere('no_batch', 'like', '%' . $search . '%')
                         ->orWhere('nama_produk', 'like', '%' . $search . '%');
        })
        ->orderBy('tanggal', 'desc')
        ->orderBy('id', 'desc')
        ->paginate(10);

        // Sync all product quantities to ensure accuracy
        $allCodes = \App\Models\Product::pluck('kode_produk');
        foreach ($allCodes as $code) {
            \App\Models\Product::syncQuantity($code);
        }

        // Fetch product codes from available/near-expiry inventory, ordered by earliest expiry date (FEFO)
        $inventoryCodesWithExpiry = \App\Models\Inventory::where('status', '!=', 'Expired')
            ->select('kode_produk', \DB::raw('MIN(tgl_expired) as earliest_expiry'))
            ->groupBy('kode_produk')
            ->orderBy('earliest_expiry', 'asc')
            ->get();
            
        $inventoryCodes = $inventoryCodesWithExpiry->pluck('kode_produk');
        
        // Fetch master products and preserve the FEFO order
        $masterProducts = \App\Models\Product::whereIn('kode_produk', $inventoryCodes)
            ->get()
            ->sortBy(function($model) use ($inventoryCodes) {
                return array_search($model->kode_produk, $inventoryCodes->toArray());
            });

        $products = $masterProducts->map(function($masterProduct) {
            $kode = $masterProduct->kode_produk;
            
            // Get all batches for this product from the inventory table (excluding expired)
            $batches = \App\Models\Inventory::where('kode_produk', $kode)
                ->where('status', '!=', 'Expired')
                ->orderBy('tgl_expired', 'asc')
                ->get();
            
            $pObj = new \stdClass();
            $pObj->kode_produk = $kode;
            $pObj->nama_produk = $masterProduct->nama_produk;
            $pObj->kategori = $masterProduct->kategori;
            $pObj->inventories = $batches;
            $pObj->jumlah = $masterProduct->jumlah;

            // Price logic: Get price from HargaProduk based on category
            $category = \App\Models\Category::where('nama_kategori', $pObj->kategori)->first();
            if ($category) {
                $hargaProduk = \App\Models\HargaProduk::where('kategori_id', $category->id)->first();
                $pObj->harga_fefo = $hargaProduk ? $hargaProduk->harga : 0;
            } else {
                $pObj->harga_fefo = 0;
            }

            return $pObj;
        });

        return view('persediaan.index', compact('entries', 'products', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Sync all product quantities to ensure accuracy
        $allCodes = \App\Models\Product::pluck('kode_produk');
        foreach ($allCodes as $code) {
            \App\Models\Product::syncQuantity($code);
        }

        // Fetch product codes from available/near-expiry inventory, ordered by earliest expiry date (FEFO)
        $inventoryCodesWithExpiry = \App\Models\Inventory::where('status', '!=', 'Expired')
            ->select('kode_produk', \DB::raw('MIN(tgl_expired) as earliest_expiry'))
            ->groupBy('kode_produk')
            ->orderBy('earliest_expiry', 'asc')
            ->get();
            
        $inventoryCodes = $inventoryCodesWithExpiry->pluck('kode_produk');
        
        // Fetch master products and preserve the FEFO order
        $masterProducts = \App\Models\Product::whereIn('kode_produk', $inventoryCodes)
            ->get()
            ->sortBy(function($model) use ($inventoryCodes) {
                return array_search($model->kode_produk, $inventoryCodes->toArray());
            });

        $products = $masterProducts->map(function($masterProduct) {
            $kode = $masterProduct->kode_produk;
            
            // Get all batches for this product from the inventory table (excluding expired)
            $batches = \App\Models\Inventory::where('kode_produk', $kode)
                ->where('status', '!=', 'Expired')
                ->orderBy('tgl_expired', 'asc')
                ->get();
            
            $pObj = new \stdClass();
            $pObj->kode_produk = $kode;
            $pObj->nama_produk = $masterProduct->nama_produk;
            $pObj->kategori = $masterProduct->kategori;
            $pObj->inventories = $batches;
            $pObj->jumlah = $masterProduct->jumlah;

            // Price logic: Get price from HargaProduk based on category
            $category = \App\Models\Category::where('nama_kategori', $pObj->kategori)->first();
            if ($category) {
                $hargaProduk = \App\Models\HargaProduk::where('kategori_id', $category->id)->first();
                $pObj->harga_fefo = $hargaProduk ? $hargaProduk->harga : 0;
            } else {
                $pObj->harga_fefo = 0;
            }

            return $pObj;
        });

        return view('persediaan.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'no_batch' => 'required|string|max:255',
            'kode_produk' => 'required|string|max:255',
            'nama_produk' => 'required|string|max:255',
            'jumlah_masuk' => 'required|integer|min:1',
            'stok_awal' => 'nullable|integer|min:0',
            'harga' => 'nullable|numeric|min:0',
            'total_harga' => 'nullable|numeric|min:0',
            'jumlah_pack' => 'nullable|integer|min:0',
            'bbb' => 'nullable|numeric|min:0',
            'btkl' => 'nullable|numeric|min:0',
            'bop' => 'nullable|numeric|min:0',
        ]);

        // Strip 'Rp ' from harga and total_harga if present
        $harga = str_replace(['Rp ', '.', ','], '', $request->harga ?? '0');
        $total_harga = str_replace(['Rp ', '.', ','], '', $request->total_harga ?? '0');
        $request->merge(['harga' => $harga, 'total_harga' => $total_harga]);

        DB::transaction(function () use ($request, $harga, $total_harga) {
            $product = Product::where('kode_produk', $request->kode_produk)->first();
            $stok_awal = null;
            
            // Find or create the inventory batch based on kode_produk and no_batch
            $inventory = \App\Models\Inventory::where('kode_produk', $request->kode_produk)
                ->where('no_batch', $request->no_batch)
                ->first();

            if ($inventory) {
                // If batch exists, increment ONLY the current quantity (jumlah)
                $inventory->jumlah += (int)$request->jumlah_masuk;
                $inventory->save();
            } else {
                // If batch doesn't exist, create a new inventory record
                $stok_awal = $request->stok_awal ?? ($product ? $product->jumlah : 0);
                $inventory = \App\Models\Inventory::create([
                    'kode_produk' => $request->kode_produk,
                    'nama_produk' => $request->nama_produk,
                    'rasa_produk' => $product->rasa_produk ?? null,
                    'no_batch' => $request->no_batch,
                    'jumlah' => (int)$request->jumlah_masuk,
                    'jumlah_per_batch' => (int)$request->jumlah_masuk,
                    'tgl_masuk' => $request->tanggal,
                    'status' => 'Tersedia',
                    'kategori' => $product->kategori ?? null,
                    'harga' => $request->harga,
                    'stok_awal' => $stok_awal,
                ]);
            }

            // Create the PersediaanEntry record
            $entryData = $request->all();
            $entryData['inventory_id'] = $inventory->id;
            $entryData['harga'] = $harga;
            $entryData['total_harga'] = $total_harga;
            $entryData['stok_awal'] = $stok_awal ?? ($inventory->stok_awal ?? 0);
            PersediaanEntry::create($entryData);

            // Create KartuStokEntry
            KartuStokEntry::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Produk Masuk (Batch: ' . ($request->no_batch ?? '-') . ')',
                'id_transaksi' => $request->id_transaksi,
                'masuk' => $request->jumlah_masuk,
                'keluar' => 0,
                'harga' => $harga,
                'total_harga' => $total_harga,
                'kode_produk' => $request->kode_produk,
                'nama_produk' => $request->nama_produk,
            ]);

            // Sync total product stock
            if ($product) {
                Product::syncQuantity($request->kode_produk);
            }

            // Create Jurnal Umum (Double Entry)
            // 140: Persediaan Barang Jadi
            // 143: Persediaan Barang dalam proses
            \App\Models\JurnalUmum::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Persediaan Produk Jadi',
                'ref' => '140',
                'debit' => $total_harga,
                'kredit' => 0,
                'id_transaksi' => $request->id_transaksi,
            ]);
            \App\Models\JurnalUmum::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Produk Dalam Proses',
                'ref' => '143',
                'debit' => 0,
                'kredit' => $total_harga,
                'id_transaksi' => $request->id_transaksi,
            ]);
        });

        return redirect()->route('persediaan.index')->with('success', 'Entry produk masuk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $entry = PersediaanEntry::findOrFail($id);
        return view('persediaan.show', compact('entry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $entry = PersediaanEntry::findOrFail($id);
        return view('persediaan.edit', compact('entry'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $entry = PersediaanEntry::findOrFail($id);

        $request->validate([
            'id_transaksi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'kode_produk' => 'required|string|max:255',
            'nama_produk' => 'required|string|max:255',
            'jumlah_masuk' => 'required|integer|min:1',
            'stok_awal' => 'required|integer|min:0',
            'harga' => 'nullable|numeric|min:0',
            'total_harga' => 'nullable|numeric|min:0',
            'no_batch' => 'nullable|string|max:255',
            'jumlah_pack' => 'nullable|integer|min:0',
            'bbb' => 'nullable|numeric|min:0',
            'btkl' => 'nullable|numeric|min:0',
            'bop' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $entry) {
            // Adjust inventory batch stock (Stock IN logic: subtract old, add new)
            if ($entry->inventory_id) {
                $inventory = \App\Models\Inventory::find($entry->inventory_id);
                if ($inventory) {
                    $inventory->jumlah -= (int)$entry->jumlah_masuk; // Remove old added amount
                    $inventory->jumlah += (int)$request->jumlah_masuk; // Add new amount
                    $inventory->save();
                }
            }

            // Clean currency strings for database
            $total_harga = str_replace(['Rp ', '.', ','], '', $request->total_harga ?? '0');
            $request->merge(['total_harga' => $total_harga]);

            // Fill with request data
            $entry->update($request->all());
            
            // Sync Product quantity
            Product::syncQuantity($entry->kode_produk);

            // Update Jurnal Umum
            \App\Models\JurnalUmum::where('id_transaksi', $entry->id_transaksi)->delete();
            
            \App\Models\JurnalUmum::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Persediaan Produk Jadi',
                'ref' => '140',
                'debit' => $total_harga,
                'kredit' => 0,
                'id_transaksi' => $request->id_transaksi,
            ]);
            \App\Models\JurnalUmum::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Produk Dalam Proses',
                'ref' => '143',
                'debit' => 0,
                'kredit' => $total_harga,
                'id_transaksi' => $request->id_transaksi,
            ]);
        });

        return redirect()->route('persediaan.index')->with('success', 'Entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entry = PersediaanEntry::findOrFail($id);
        
        DB::transaction(function () use ($entry) {
            if ($entry->inventory_id) {
                $inventory = \App\Models\Inventory::find($entry->inventory_id);
                if ($inventory) {
                    $inventory->jumlah -= (int)$entry->jumlah_masuk; // Remove the added amount
                    $inventory->save();
                }
            }
            
            $kode_produk = $entry->kode_produk;
            $entry->delete();
            
            Product::syncQuantity($kode_produk);

            // Delete related Jurnal Umum
            \App\Models\JurnalUmum::where('id_transaksi', $entry->id_transaksi)->delete();
        });

        return redirect()->route('persediaan.index')->with('success', 'Entry deleted successfully.');
    }
}
