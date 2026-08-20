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
            ->get();

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

        $categories = \App\Models\Category::all();

        return view('persediaan.index', compact('entries', 'products', 'search', 'inventories', 'categories'));
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

        // Fetch all inventories to show in dropdown
        $inventories = \App\Models\Inventory::orderBy('tgl_expired', 'asc')
            ->get();

        $products = $inventories->map(function($inv) {
            $kode = $inv->kode_produk;
            $masterProduct = \App\Models\Product::where('kode_produk', $kode)->first();
            
            $pObj = new \stdClass();
            $pObj->kode_produk = $kode;
            $pObj->inventory_id = $inv->id;
            $pObj->nama_produk = $inv->nama_produk;
            $pObj->no_batch = $inv->no_batch;
            $pObj->kategori = $inv->kategori;
            $pObj->jumlah = $inv->jumlah;

            // Price logic
            $pObj->harga_fefo = $masterProduct ? ($masterProduct->harga ?? 0) : ($inv->harga ?? 0);

            return $pObj;
        })->values();

        $categories = \App\Models\Category::all();

        return view('persediaan.create', compact('products', 'categories'));
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
            'kode_produk' => 'nullable|string|max:255',
            'nama_produk' => 'nullable|string|max:255',
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

        // Ambil inventory yang dipilih dari dropdown (nilai dikirim langsung oleh browser)
        $selectedInventoryId = $request->inventory_id_select ?: $request->inventory_id;

        if (empty($selectedInventoryId)) {
            return back()->withInput()->withErrors(['kode_produk' => 'Pilih produk terlebih dahulu.']);
        }

        $selectedInventory = \App\Models\Inventory::find($selectedInventoryId);

        if (!$selectedInventory) {
            return back()->withInput()->withErrors(['kode_produk' => 'Produk tidak ditemukan.']);
        }

        // Pastikan kode_produk dan nama_produk terisi dari inventory yang dipilih
        $request->merge([
            'kode_produk'  => $selectedInventory->kode_produk,
            'nama_produk'  => $request->nama_produk ?: $selectedInventory->nama_produk,
            'inventory_id' => $selectedInventory->id,
        ]);

        // Validasi duplikasi dihapus dari sini sesuai permintaan user,
        // karena di menu ini (Tambah Entry Produk Masuk) user justru 
        // sedang menambah stok ke batch yang sudah ada (auto-fill).



        // Calculate Persediaan Produk Jadi (Total Harga) and HPP
        // Strip Indonesian number formatting (e.g. "73.202,70" -> 73202.70)
        $parseNum = fn($val) => (float) str_replace(['.', ','], ['', '.'], preg_replace('/[^0-9.,]/', '', $val ?? '0'));

        $jumlah = (int) ($request->jumlah_masuk ?? 0);
        
        // Gunakan HPP manual jika diisi
        $hpp = $request->filled('harga_pokok_produksi') ? $parseNum($request->harga_pokok_produksi) : 0;
        
        // Total Harga dihitung dari HPP * Jumlah Pack Masuk
        $total_harga = $hpp * $jumlah;

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
            
            // Gunakan batch yang dipilih oleh user
            $inventory = \App\Models\Inventory::where('id', $request->inventory_id)->first();

            if ($inventory) {
                // Simpan jumlah SEBELUM ditambah, untuk dipakai sebagai stok_awal di Kartu Stok
                $jumlah_sebelum = (int)$inventory->jumlah;

                // If batch exists, increment ONLY the current quantity (jumlah)
                $inventory->jumlah += (int)$request->jumlah_masuk;
                // Update hpp, margin, harga_dasar_jual
                $inventory->hpp = $request->harga_pokok_produksi ?? null;
                $inventory->margin = $request->margin ?? null;
                $inventory->harga_dasar_jual = $request->harga_dasar_jual ?? null;
                $inventory->save();
                
                // Check if this is the FIRST transaction for this batch
                $isFirstEntry = \App\Models\PersediaanEntry::where('inventory_id', $inventory->id)->doesntExist();
                
                if ($isFirstEntry) {
                    // Gunakan stok aktual sebelum penambahan sebagai stok_awal
                    $stok_awal_untuk_entry = $jumlah_sebelum;
                } else {
                    $stok_awal_untuk_entry = 0;
                }
            } else {
                // If batch doesn't exist, create a new inventory record
                $stok_awal = $request->stok_awal ?? ($product ? $product->jumlah : 0);
                $stok_awal_untuk_entry = $stok_awal;
                
                $inventory = \App\Models\Inventory::create([
                    'kode_produk' => $request->kode_produk,
                    'nama_produk' => $request->nama_produk,
                    'rasa_produk' => $product->rasa_produk ?? null,
                    'no_batch' => $request->no_batch,
                    'jumlah' => $stok_awal + (int)$request->jumlah_masuk, // stok_awal + jumlah_masuk
                    'jumlah_per_batch' => $stok_awal + (int)$request->jumlah_masuk,
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
            $entryData['stok_awal'] = $stok_awal_untuk_entry;
            $persediaanEntry = PersediaanEntry::create($entryData);

            // Create KartuStokEntry (Include stok_awal in the FIRST entry's Masuk column)
            KartuStokEntry::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Produk Masuk (Batch: ' . ($request->no_batch ?? '-') . ')',
                'id_transaksi' => $request->id_transaksi,
                'masuk' => $stok_awal_untuk_entry + $request->jumlah_masuk, // Sum them for the ledger!
                'keluar' => 0,
                'harga' => $harga,
                'total_harga' => $total_harga,
                'kode_produk' => $request->kode_produk,
                'nama_produk' => $request->nama_produk,
                'no_batch' => $request->no_batch,
            ]);

            // Sync total product stock
            if ($product) {
                Product::syncQuantity($request->kode_produk);
            }

            // Create Jurnal double-entry:
            //   D 140 Persediaan Produk Jadi
            //   K 143 Produk Dalam Proses
            $this->createJurnalPersediaan(
                $request->tanggal,
                'Produk Masuk: ' . $request->nama_produk,
                $total_harga,
                $persediaanEntry->id,
                $request->id_transaksi
            );
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

            $jumlah = (int) ($request->jumlah_masuk ?? 0);
            
            // Gunakan HPP manual jika diisi
            $hpp = $request->filled('harga_pokok_produksi') ? $parseNum($request->harga_pokok_produksi) : 0;
            
            // Total Harga dihitung dari HPP * Jumlah Pack Masuk
            $total_harga = $hpp * $jumlah;

            $request->merge([
                'total_harga' => $total_harga,
                'harga_pokok_produksi' => $hpp
            ]);

            // Fill with request data
            $entry->update($request->all());
            
            // Sync Product quantity
            Product::syncQuantity($entry->kode_produk);

            // Hapus jurnal lama lalu buat ulang
            \App\Models\JurnalUmum::where('id_transaksi', $entry->id_transaksi)->delete();

            $this->createJurnalPersediaan(
                $request->tanggal,
                'Produk Masuk: ' . $entry->nama_produk,
                $total_harga,
                $entry->id,
                $request->id_transaksi
            );
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
            
            $kode_produk    = $entry->kode_produk;
            $id_transaksi   = $entry->id_transaksi;
            $entry_id       = $entry->id;
            $entry->delete();
            
            Product::syncQuantity($kode_produk);

            // Delete related JurnalUmum + linked Jurnal double-entry
            \App\Models\JurnalUmum::where('id_transaksi', $id_transaksi)->delete();
            \App\Models\Jurnal::where('no_referensi', $id_transaksi)->each(function ($j) {
                $j->details()->delete();
                $j->delete();
            });
        });

        return redirect()->route('persediaan.index')->with('success', 'Entry deleted successfully.');
    }

    /**
     * Create double-entry rows in jurnal_umum for inventory intake:
     *   D 140 Persediaan Produk Jadi  = nilai persediaan produk jadi
     *   K 143 Produk Dalam Proses     = nilai persediaan produk jadi
     */
    protected function createJurnalPersediaan(
        string $tanggal,
        string $nama_produk,
        float  $nominal,
        int    $ref_id,
        string $id_transaksi
    ): void {
        // Baris 1 – DEBIT: Persediaan Produk Jadi
        \App\Models\JurnalUmum::create([
            'tanggal'      => $tanggal,
            'keterangan'   => 'Persediaan Produk Jadi',
            'ref'          => '140',
            'debit'        => $nominal,
            'kredit'       => 0,
            'ref_type'     => 'Persediaan',
            'ref_id'       => $ref_id,
            'id_transaksi' => $id_transaksi,
        ]);

        // Baris 2 – KREDIT: Produk Dalam Proses
        \App\Models\JurnalUmum::create([
            'tanggal'      => $tanggal,
            'keterangan'   => 'Produk Dalam Proses',
            'ref'          => '143',
            'debit'        => 0,
            'kredit'       => $nominal,
            'ref_type'     => 'Persediaan',
            'ref_id'       => $ref_id,
            'id_transaksi' => $id_transaksi,
        ]);
    }
}
