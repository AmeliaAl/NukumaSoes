<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'asc')->get();
        $flavors = \App\Models\Flavor::latest()->get();

        return view('categories.index', compact('categories', 'flavors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'berat' => 'nullable|string|max:255',
            'jenis_kemasan' => 'nullable|string|max:255',
            'masa_simpan' => 'nullable|integer|min:0',
            'satuan_masa_simpan' => 'required|in:hari,bulan,tahun',
            'deskripsi' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $kategori)
    {
        return view('categories.edit', compact('kategori'));
    }

    public function update(Request $request, Category $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'berat' => 'nullable|string|max:255',
            'jenis_kemasan' => 'nullable|string|max:255',
            'masa_simpan' => 'nullable|integer|min:0',
            'satuan_masa_simpan' => 'required|in:hari,bulan,tahun',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all());

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $kategori)
    {
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
