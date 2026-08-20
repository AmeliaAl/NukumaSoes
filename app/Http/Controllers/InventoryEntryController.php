<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
use App\Models\Flavor;
use App\Models\Product;
use App\Models\PersediaanEntry;
use App\Models\ProdukKeluarEntry;
use App\Models\KartuStokEntry;
use App\Models\ExpiredProductHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryEntryController extends Controller
{
    /**
     * Show the form for creating a new inventory entry.
     */
    public function create()
    {
        $products = Product::orderBy('kode_produk', 'asc')->get();
        $categories = Category::all();
        $flavors = Flavor::all();
        return view('inventory.create', compact('products', 'categories', 'flavors'));
    }

    /**
     * Store a newly created inventory entry in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'nullable|string|max:255',
            'no_batch' => 'nullable|string|max:255',
            'nama_produk' => 'required|string|max:255',
            'rasa_produk' => 'nullable|string|max:255',
            'kategori' => 'required|string|max:255',
            'stok_awal' => 'nullable|integer|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'tgl_masuk' => 'required|date',
            'masa_simpan' => 'required|integer|min:0',
        ]);

        // Cek duplikasi: kombinasi kode_produk + no_batch tidak boleh ada di inventories
        if ($request->filled('kode_produk') && $request->filled('no_batch')) {
            $duplikat = \App\Models\Inventory::where('kode_produk', $request->kode_produk)
                ->where('no_batch', $request->no_batch)
                ->exists();

            if ($duplikat) {
                return back()
                    ->withInput()
                    ->withErrors(['no_batch' => 'No batch ini sudah digunakan untuk ID produk yang sama.']);
            }
        }


        $data = $request->all();
        // Get jenis_produk from the selected product
        $selectedProduct = Product::where('kode_produk', $request->kode_produk)->first();
        $data['jenis_produk'] = $selectedProduct ? ($selectedProduct->jenis_produk ?? 'Brand Sendiri') : 'Brand Sendiri';
        $tglMasuk = Carbon::parse($data['tgl_masuk']);
        $masaSimpan = (int) $data['masa_simpan'];
        $unit = $data['satuan_masa_simpan'] ?? 'hari';

        // Calculate tgl_expired based on dynamic units
        if ($unit === 'hari') {
            $data['tgl_expired'] = $tglMasuk->copy()->addDays($masaSimpan);
        } elseif ($unit === 'bulan') {
            $data['tgl_expired'] = $tglMasuk->copy()->addMonths($masaSimpan);
        } elseif ($unit === 'tahun') {
            $data['tgl_expired'] = $tglMasuk->copy()->addYears($masaSimpan);
        } else {
            $data['tgl_expired'] = $tglMasuk->copy()->addDays($masaSimpan);
        }
        
        // Calculate sisa_hari and status
        $now = Carbon::now();
        $data['sisa_hari'] = (int) $now->diffInDays($data['tgl_expired'], false);
        
        if ($data['sisa_hari'] > 30) {
            $data['status'] = 'Aman';
        } elseif ($data['sisa_hari'] >= 1) {
            $data['status'] = 'Hampir Expired';
        } else {
            $data['status'] = 'Expired';
        }

        $data['harga'] = (float) $data['harga_jual'];
        $data['stok_awal'] = $data['stok_awal'] ?? 0;
        $data['jumlah_per_batch'] = $data['stok_awal'];
        $data['jumlah'] = $data['stok_awal'];
        $totalHarga = $data['jumlah'] * $data['harga'];

        // Save to inventories table
        $inventory = Inventory::create($data);

        // Sync Product Quantity
        Product::syncQuantity($data['kode_produk']);

        // Jurnal Umum TIDAK dibuat di sini.
        // Jurnal hanya dibuat saat transaksi: Produk Masuk, Produk Keluar, atau Produk Expired.

        return redirect()->route('persediaan-produk.index')->with('success', 'Persediaan produk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified inventory entry.
     */
    public function edit(Inventory $inventory_entry)
    {
        $inventory = $inventory_entry;
        $products = Product::orderBy('kode_produk', 'asc')->get();
        $categories = Category::all();
        $flavors = Flavor::all();
        return view('inventory.edit', compact('inventory', 'products', 'categories', 'flavors'));
    }

    /**
     * Update the specified inventory entry in storage.
     */
    public function update(Request $request, Inventory $inventory_entry)
    {
        $inventory = $inventory_entry;
        $request->validate([
            'kode_produk' => 'nullable|string|max:255',
            'no_batch' => 'nullable|string|max:255',
            'nama_produk' => 'required|string|max:255',
            'rasa_produk' => 'nullable|string|max:255',
            'kategori' => 'required|string|max:255',
            'stok_awal' => 'nullable|integer|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'tgl_masuk' => 'required|date',
            'masa_simpan' => 'required|integer|min:0',
        ]);

        // Cek duplikasi: kombinasi kode_produk + no_batch tidak boleh ada di inventories selain dirinya sendiri
        if ($request->filled('kode_produk') && $request->filled('no_batch')) {
            $duplikat = \App\Models\Inventory::where('kode_produk', $request->kode_produk)
                ->where('no_batch', $request->no_batch)
                ->where('id', '!=', $inventory->id)
                ->exists();

            if ($duplikat) {
                return back()
                    ->withInput()
                    ->withErrors(['no_batch' => 'No batch ini sudah digunakan untuk ID produk yang sama.']);
            }
        }


        $data = $request->all();
        // Get jenis_produk from the selected product
        $selectedProduct = Product::where('kode_produk', $request->kode_produk)->first();
        $data['jenis_produk'] = $selectedProduct ? ($selectedProduct->jenis_produk ?? 'Brand Sendiri') : 'Brand Sendiri';
        $tglMasuk = Carbon::parse($data['tgl_masuk']);
        $masaSimpan = (int) $data['masa_simpan'];
        $unit = $data['satuan_masa_simpan'] ?? 'hari';

        // Calculate tgl_expired based on dynamic units
        if ($unit === 'hari') {
            $data['tgl_expired'] = $tglMasuk->copy()->addDays($masaSimpan);
        } elseif ($unit === 'bulan') {
            $data['tgl_expired'] = $tglMasuk->copy()->addMonths($masaSimpan);
        } elseif ($unit === 'tahun') {
            $data['tgl_expired'] = $tglMasuk->copy()->addYears($masaSimpan);
        } else {
            $data['tgl_expired'] = $tglMasuk->copy()->addDays($masaSimpan);
        }
        
        // Calculate sisa_hari and status
        $now = Carbon::now();
        $data['sisa_hari'] = (int) $now->diffInDays($data['tgl_expired'], false);
        
        if ($data['sisa_hari'] > 30) {
            $data['status'] = 'Aman';
        } elseif ($data['sisa_hari'] >= 1) {
            $data['status'] = 'Hampir Expired';
        } else {
            $data['status'] = 'Expired';
        }

        $data['harga'] = (float) $data['harga_jual'];
        $data['stok_awal'] = $data['stok_awal'] ?? 0;
        $data['jumlah_per_batch'] = $data['stok_awal'];
        $data['jumlah'] = $data['stok_awal'];

        $inventory->update($data);

        // Sync Product Quantity
        Product::syncQuantity($inventory->kode_produk);

        return redirect()->route('persediaan-produk.index')->with('success', 'Persediaan produk berhasil diperbarui.');
    }

    /**
     * Remove the specified inventory entry from storage.
     */
    public function destroy(Inventory $inventory_entry)
    {
        $inventory = $inventory_entry;
        $kode_produk = $inventory->kode_produk;

        if ($inventory->status === 'Expired') {
            \App\Models\ExpiredProductHistory::create([
                'no_batch'         => $inventory->no_batch ?? '-',
                'kode_produk'      => $inventory->kode_produk ?? null,
                'nama_produk'      => $inventory->nama_produk ?? 'Tidak Diketahui',
                'rasa_produk'      => $inventory->rasa_produk ?? null,
                'kategori'         => $inventory->kategori ?? '-',
                'jumlah_per_batch' => $inventory->jumlah_per_batch ?? 0,
                'jumlah'           => $inventory->jumlah ?? 0,
                'harga'            => $inventory->harga ?? 0,
                'hpp'              => $inventory->hpp ?? 0,
                'total'            => ($inventory->jumlah ?? 0) * ($inventory->hpp ?? 0),
                'tgl_masuk'        => $inventory->tgl_masuk ?? now(),
                'tgl_expired'      => $inventory->tgl_expired ?? now(),
                'status'           => $inventory->status ?? 'Expired',
                'sisa_hari'        => $inventory->sisa_hari ?? 0,
                'is_journaled'     => false,
            ]);
        }

        $inventory->delete();

        // Sync Product Quantity
        Product::syncQuantity($kode_produk);

        $message = 'Persediaan produk berhasil dihapus.';
        if ($inventory->status === 'Expired') {
            $message .= ' Data masuk ke Riwayat Produk Expired.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show form for product entry (Masuk).
     */
    public function masuk(Inventory $inventory)
    {
        return view('inventory.masuk-batch', compact('inventory'));
    }

    /**
     * Store product entry (Masuk).
     */
    public function storeMasuk(Request $request, Inventory $inventory)
    {
        $request->validate([
            'id_transaksi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jumlah_masuk' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $inventory) {
            $jumlah = $request->jumlah_masuk;
            $harga = $request->harga;
            $total_harga = $jumlah * $harga;

            // Update Inventory
            $inventory->jumlah += $jumlah;
            $inventory->save();

            // Create PersediaanEntry (Log)
            PersediaanEntry::create([
                'id_transaksi' => $request->id_transaksi,
                'tanggal' => $request->tanggal,
                'kode_produk' => $inventory->kode_produk,
                'nama_produk' => $inventory->nama_produk,
                'jumlah_masuk' => $jumlah,
                'harga' => $harga,
                'total_harga' => $total_harga,
                'no_batch' => $inventory->kode_produk, // Using kode_produk as batch for now if no specific batch field
            ]);

            // Create KartuStokEntry
            KartuStokEntry::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Produk Masuk (Batch ' . $inventory->kode_produk . ')',
                'id_transaksi' => $request->id_transaksi,
                'masuk' => $jumlah,
                'keluar' => 0,
                'harga' => $harga,
                'total_harga' => $total_harga,
                'kode_produk' => $inventory->kode_produk,
                'nama_produk' => $inventory->nama_produk,
            ]);

            // Sync Product Quantity
            Product::syncQuantity($inventory->kode_produk);

            // Create Jurnal Umum
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

        return redirect()->route('persediaan-produk.index')->with('success', 'Stok batch berhasil ditambah.');
    }

    /**
     * Show form for product exit (Keluar).
     */
    public function keluar(Inventory $inventory)
    {
        return view('inventory.keluar-batch', compact('inventory'));
    }

    /**
     * Store product exit (Keluar).
     */
    public function storeKeluar(Request $request, Inventory $inventory)
    {
        $request->validate([
            'id_transaksi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jumlah_keluar' => 'required|integer|min:1|max:' . $inventory->jumlah,
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:lunas,belum_lunas',
        ]);

        DB::transaction(function () use ($request, $inventory) {
            $jumlah = $request->jumlah_keluar;
            $harga = $request->harga;
            $total_harga = $jumlah * $harga;

            // Update Inventory
            $inventory->jumlah -= $jumlah;
            $inventory->save();

            // Create ProdukKeluarEntry (Log)
            ProdukKeluarEntry::create([
                'id_transaksi' => $request->id_transaksi,
                'tanggal' => $request->tanggal,
                'kode_produk' => $inventory->kode_produk,
                'nama_produk' => $inventory->nama_produk,
                'jumlah_keluar' => $jumlah,
                'harga' => $harga,
                'total_harga' => $total_harga,
                'status' => $request->status,
                'jenis' => 'keluar',
            ]);

            // Create KartuStokEntry
            KartuStokEntry::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Produk Keluar (Batch ' . $inventory->kode_produk . ')',
                'id_transaksi' => $request->id_transaksi,
                'masuk' => 0,
                'keluar' => $jumlah,
                'harga' => $harga,
                'total_harga' => $total_harga,
                'kode_produk' => $inventory->kode_produk,
                'nama_produk' => $inventory->nama_produk,
            ]);

            // Sync Product Quantity
            Product::syncQuantity($inventory->kode_produk);

            // Create Jurnal Umum
            $coaPersediaanProduk = \App\Models\Coa::where('nama_akun', 'LIKE', '%Persediaan Produk%')->where('nama_akun', 'NOT LIKE', '%Jadi%')->first();
            $refPersediaanProduk = $coaPersediaanProduk ? $coaPersediaanProduk->kode_akun : '115';

            $coaPersediaanJadi = \App\Models\Coa::where('nama_akun', 'LIKE', '%Persediaan Produk Jadi%')->first();
            $refPersediaanJadi = $coaPersediaanJadi ? $coaPersediaanJadi->kode_akun : '113';

            \App\Models\JurnalUmum::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Persediaan Produk',
                'ref' => $refPersediaanProduk,
                'debit' => $total_harga,
                'kredit' => 0,
            ]);

            \App\Models\JurnalUmum::create([
                'tanggal' => $request->tanggal,
                'keterangan' => 'Persediaan Produk Jadi',
                'ref' => $refPersediaanJadi,
                'debit' => 0,
                'kredit' => $total_harga,
            ]);
        });

        return redirect()->route('persediaan-produk.index')->with('success', 'Stok batch berhasil dikurangi.');
    }
}
