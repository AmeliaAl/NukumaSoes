<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HargaProduk;

class HargaProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hargaProduks = HargaProduk::latest()->get();

        return view('harga-produk.index', compact('hargaProduks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::orderBy('nama_kategori', 'asc')->get();
        return view('harga-produk.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:categories,id',
            'jenis_mitra' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        HargaProduk::create($request->all());

        return redirect()->route('harga-produk.index')->with('success', 'Harga kategori berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HargaProduk $hargaProduk)
    {
        $categories = \App\Models\Category::orderBy('nama_kategori', 'asc')->get();
        return view('harga-produk.edit', compact('hargaProduk', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HargaProduk $hargaProduk)
    {
        $request->validate([
            'kategori_id' => 'required|exists:categories,id',
            'jenis_mitra' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $hargaProduk->update($request->all());

        return redirect()->route('harga-produk.index')->with('success', 'Harga produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HargaProduk $hargaProduk)
    {
        $hargaProduk->delete();

        return redirect()->route('harga-produk.index')->with('success', 'Harga produk berhasil dihapus.');
    }
}
?>

