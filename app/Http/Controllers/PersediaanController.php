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

        // Fetch all inventories for the dropdown (keyed by inventory id), including expired
        $inventories = \App\Models\Inventory::orderBy('tgl_expired', 'asc')
            ->get()
            ->unique('kode_produk');

        // Build $products from those inventories (for JS productsData compatibility)
        $products = $inventories->map(function($inv) {
            $pObj = new \stdClass();
            $pObj->kode_produk = $inv->id; // use inventory id as the value
            $pObj->nama_produk = $inv->nama_produk;
            $pObj->kategori = $inv->kategori;
            $pObj->no_batch = $inv->no_batch;
            $pObj->jumlah = $inv->jumlah;
            $pObj->inventories = collect([$inv]);

            // Price logic
            $pObj->harga_fefo = $inv->harga ?? 0;

            return $pObj;
        });

        return view('persediaan.index', compact('entries', 'products', 'search', 'inventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Sync all inventory statuses based on today's date
        \App\Models\Inventory::syncAllStatus();

        // Sync all product quantities to ensure accuracy
        $allCodes = \App\Models\Product::pluck('kode_produk');
        foreach ($allCodes as $code) {
            \App\Models\Product::syncQuantity($code);
        }

        // Fetch active inventories (Aman or Hampir Expired)
        // This explicitly excludes expired ones and nulls (which UI treats as expired)
        $inventories = \App\Models\Inventory::whereNotNull('tgl_expired')
            ->whereDate('tgl_expired', '>', now())
            ->orderBy('tgl_expired', 'asc')
            ->get();

        $products = $inventories->map(function($inv) {
            $kode = $inv->kode_produk;
            $masterProduct = \App\Models\Product::where('kode_produk', $kode)->first();
            
            $pObj = new \stdClass();
            $pObj->kode_produk = $kode;
            $pObj->nama_produk = $inv->nama_produk;
            $pObj->no_batch = $inv->no_batch;
            $pObj->kategori = $inv->kategori;
            $pObj->jumlah = $inv->jumlah;

            // Price logic
            $pObj->harga_fefo = $masterProduct ? ($masterProduct->harga ?? 0) : ($inv->harga ?? 0);

            return $pObj;
        })->values();

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
            'harga_pokok_produksi' => 'nullable|numeric|min:0',
            'bbb' => 'nullable|numeric|min:0',
            'btkl' => 'nullable|numeric|min:0',
            'bop' => 'nullable|numeric|min:0',
            'harga_dasar_jual' => 'nullable|numeric|min:0',
            'margin' => 'nullable|numeric|min:0',
        ]);

        // Calculate Persediaan Produk Jadi (Total Harga) and HPP
        // Strip Indonesian number formatting (e.g. "73.202,70" -> 73202.70)
        $parseNum = fn($val) => (float) str_replace(['.', ','], ['', '.'], preg_replace('/[^0-9.,]/', '', $val ?? '0'));

        $bbb  = $parseNum($request->bbb);
        $btkl = $parseNum($request->btkl);
        $bop  = $parseNum($request->bop);
        $jumlah = (int) ($request->jumlah_masuk ?? 0);
        
        // Gunakan HPP manual jika diisi
        $hpp = $request->filled('harga_pokok_produksi') ? $parseNum($request->harga_pokok_produksi) : 0;
        
        // Total Harga dihitung dari (BBB + BTKL + BOP) * Jumlah Pack Masuk
        $total_harga = ($bbb + $btkl + $bop) * $jumlah;

        // Strip 'Rp ' from harga if present
        $harga = str_replace(['Rp ', '.', ','], '', $request->harga ?? '0');
        $request->merge([
            'harga' => $harga, 
            'total_harga' => $total_harga,
            'harga_pokok_produksi' => $hpp
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $harga, $total_harga) {
            $product = Product::where('kode_produk', $request->kode_produk)->first();
            $stok_awal = null;
            
            // FEFO: pilih batch paling awal expired untuk produk yang dipilih
            // Jika user isi no_batch manual, sistem tetap mengikuti FEFO sesuai permintaan.
            $inventory = \App\Models\Inventory::where('kode_produk', $request->kode_produk)
                ->where('status', '!=', 'Expired')
                ->orderBy('tgl_expired', 'asc')
                ->first();

            if ($inventory) {
                // If batch exists, increment ONLY the current quantity (jumlah)
                $inventory->jumlah += (int)$request->jumlah_masuk;
                // Update hpp, margin, harga_dasar_jual
                $inventory->hpp = $request->harga_pokok_produksi ?? null;
                $inventory->margin = $request->margin ?? null;
                $inventory->harga_dasar_jual = $request->harga_dasar_jual ?? null;
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
                    'hpp' => $request->harga_pokok_produksi ?? null,
                    'margin' => $request->margin ?? null,
                    'harga_dasar_jual' => $request->harga_dasar_jual ?? null,
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
        $inventories = \App\Models\Inventory::with('product')
            ->whereDate('tgl_expired', '>', now())
            ->orderBy('tgl_expired', 'asc')
            ->get();
        return view('persediaan.edit', compact('entry', 'inventories'));
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
            'harga_pokok_produksi' => 'nullable|numeric|min:0',
            'bbb' => 'nullable|numeric|min:0',
            'btkl' => 'nullable|numeric|min:0',
            'bop' => 'nullable|numeric|min:0',
            'harga_dasar_jual' => 'nullable|numeric|min:0',
            'margin' => 'nullable|numeric|min:0',
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

            // Calculate Persediaan Produk Jadi (Total Harga) and HPP
            // Strip Indonesian number formatting (e.g. "73.202,70" -> 73202.70)
            $parseNum = fn($val) => (float) str_replace(['.', ','], ['', '.'], preg_replace('/[^0-9.,]/', '', $val ?? '0'));

            $bbb  = $parseNum($request->bbb);
            $btkl = $parseNum($request->btkl);
            $bop  = $parseNum($request->bop);
            $jumlah = (int) ($request->jumlah_masuk ?? 0);
            
            // Gunakan HPP manual jika diisi
            $hpp = $request->filled('harga_pokok_produksi') ? $parseNum($request->harga_pokok_produksi) : 0;
            
            // Total Harga dihitung dari (BBB + BTKL + BOP) * Jumlah Pack Masuk
            $total_harga = ($bbb + $btkl + $bop) * $jumlah;

            $request->merge([
                'total_harga' => $total_harga,
                'harga_pokok_produksi' => $hpp
            ]);

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
