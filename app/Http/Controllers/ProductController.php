<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Flavor;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::orderBy('tgl_expired', 'asc');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('rasa')) {
            $query->where('rasa_produk', $request->rasa);
        }

        $products = $query->get();
        $categories = Category::all();
        $flavors = Flavor::all();

        return view('produk.index', compact('products', 'categories', 'flavors'));
    }

    /**
     * Display a detailed inventory listing.
     */
    public function inventory(Request $request)
    {
        $query = Inventory::query()->orderBy('tgl_expired', 'asc');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('rasa')) {
            $query->where('rasa_produk', $request->rasa);
        }

        $inventories = $query->get();
        $categories = Category::all();
        
        return view('produk.inventory', compact('inventories', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $flavors = Flavor::all();
        return view('produk.create', compact('categories', 'flavors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|string|max:255|unique:products,kode_produk',
            'nama_produk' => 'required|string|max:255',
            'rasa_produk' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'kategori' => 'required|string|max:255',
        ]);

        $data = $request->all();
        
        // Default values for common fields
        $data['tgl_masuk'] = \Carbon\Carbon::now()->toDateString();
        $data['jumlah'] = 0; // Initialize with 0 stock
        $data['harga'] = 0;
        
        // Calculate Expiry Data
        $tglMasuk = \Carbon\Carbon::parse($data['tgl_masuk']);
        $masaSimpan = (int) ($data['masa_simpan'] ?? 0);
        $unit = $data['satuan_masa_simpan'] ?? 'hari';
        
        switch ($unit) {
            case 'hari': $data['tgl_expired'] = $tglMasuk->addDays($masaSimpan); break;
            case 'bulan': $data['tgl_expired'] = $tglMasuk->addMonths($masaSimpan); break;
            case 'tahun': $data['tgl_expired'] = $tglMasuk->addYears($masaSimpan); break;
        }

        $data['sisa_hari'] = \Carbon\Carbon::now()->diffInDays($data['tgl_expired'], false);
        $data['status'] = $data['sisa_hari'] > 30 ? 'Aman' : ($data['sisa_hari'] > 0 ? 'Akan Expired' : 'Expired');

        Product::create($data);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('produk.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $flavors = Flavor::all();
        return view('produk.edit', compact('product', 'categories', 'flavors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'kode_produk' => 'required|string|max:255|unique:products,kode_produk,' . $product->id,
            'nama_produk' => 'required|string|max:255',
            'rasa_produk' => 'nullable|string|max:255',
            'kategori' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_jual' => 'required|numeric|min:0',
            'masa_simpan' => 'required|integer|min:0',
            'satuan_masa_simpan' => 'required|in:hari,bulan,tahun',
        ]);

        $data = $request->all();

        // If tgl_masuk is not in request, keep the old one
        if (!$request->has('tgl_masuk')) {
            $data['tgl_masuk'] = $product->tgl_masuk->toDateString();
        }

        $tglMasuk = \Carbon\Carbon::parse($data['tgl_masuk']);
        $masaSimpan = (int) $data['masa_simpan'];
        switch ($data['satuan_masa_simpan']) {
            case 'hari':
                $data['tgl_expired'] = $tglMasuk->addDays($masaSimpan);
                break;
            case 'bulan':
                $data['tgl_expired'] = $tglMasuk->addMonths($masaSimpan);
                break;
            case 'tahun':
                $data['tgl_expired'] = $tglMasuk->addYears($masaSimpan);
                break;
        }
        
        // Calculate sisa_hari and status
        $now = \Carbon\Carbon::now();
        $data['sisa_hari'] = $now->diffInDays($data['tgl_expired'], false);
        
        $oneMonthFromNow = \Carbon\Carbon::now()->addMonth();

        if ($data['tgl_expired']->gt($oneMonthFromNow)) {
            $data['status'] = 'Aman';
        } elseif ($data['sisa_hari'] > 0) {
            $data['status'] = 'Akan Expired';
        } else {
            $data['status'] = 'Expired';
        }
        
        // Ensure base fields have defaults if missing
        if (!isset($data['jumlah'])) {
            $data['jumlah'] = $product->jumlah ?? 0;
        }
        if (!isset($data['harga'])) {
            $data['harga'] = $product->harga ?? 0;
        }

        $product->update($data);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
