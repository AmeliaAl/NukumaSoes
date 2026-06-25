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
        $query = Product::orderBy('kode_produk', 'asc');

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

        // Attach hpp, margin, harga_dasar_jual directly from inventory columns
        // Also attach harga_per_pcs from harga_produk table based on category
        $inventories->each(function($inv) {
            $inv->hpp = $inv->hpp;
            $inv->margin = $inv->margin;
            $inv->harga_dasar_jual = $inv->harga_dasar_jual;

            // Removed fallback to HargaProduk table, just use product harga
            $inv->harga_per_pcs = $inv->harga;
        });

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
            'kode_produk'  => 'required|string|max:255|unique:products,kode_produk',
            'nama_produk'  => 'required|string|max:255',
            'rasa_produk'  => 'nullable|string|max:255',
            'satuan'       => 'required|string|max:50',
            'kategori'     => 'required|string|max:255',
            'jenis_produk' => 'required|in:Brand Sendiri,Maklon',
            'harga'        => 'required|numeric|min:0',
            'bbb'          => 'nullable|numeric|min:0',
            'btkl'         => 'nullable|numeric|min:0',
            'bop'          => 'nullable|numeric|min:0',
            'hpp'          => 'nullable|numeric|min:0',
        ]);

        Product::create([
            'kode_produk'  => $request->kode_produk,
            'nama_produk'  => $request->nama_produk,
            'rasa_produk'  => $request->rasa_produk,
            'satuan'       => $request->satuan,
            'kategori'     => $request->kategori,
            'jenis_produk' => $request->jenis_produk,
            'tgl_masuk'    => \Carbon\Carbon::now()->toDateString(),
            'jumlah'       => 0,
            'harga'        => $request->harga,
            'bbb'          => $request->bbb,
            'btkl'         => $request->btkl,
            'bop'          => $request->bop,
            'hpp'          => $request->hpp,
            'masa_simpan'  => 0,
            'satuan_masa_simpan' => 'hari',
            'tgl_expired'  => \Carbon\Carbon::now()->addYears(1)->toDateString(),
            'sisa_hari'    => 365,
            'status'       => 'Aman',
        ]);

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
            'jenis_produk' => 'nullable|in:Brand Sendiri,Maklon',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
            'bbb'   => 'nullable|numeric|min:0',
            'btkl'  => 'nullable|numeric|min:0',
            'bop'   => 'nullable|numeric|min:0',
            'hpp'   => 'nullable|numeric|min:0',
        ]);

        $product->update([
            'kode_produk'   => $request->kode_produk,
            'nama_produk'   => $request->nama_produk,
            'rasa_produk'   => $request->rasa_produk,
            'kategori'      => $request->kategori,
            'jenis_produk'  => $request->jenis_produk ?? 'Brand Sendiri',
            'satuan'        => $request->satuan,
            'harga'         => $request->harga,
            'bbb'           => $request->bbb,
            'btkl'          => $request->btkl,
            'bop'           => $request->bop,
            'hpp'           => $request->hpp,
        ]);

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
