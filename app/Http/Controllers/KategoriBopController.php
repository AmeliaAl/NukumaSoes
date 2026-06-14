<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriBop;

class KategoriBopController extends Controller
{
    public function index()
    {
        $kategoriBop = KategoriBop::withCount('biayaOverheadPabrik')->get();
        return view('master.kategori-bop.index', compact('kategoriBop'));
    }

    public function create()
    {
        return view('master.kategori-bop.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_bop,nama_kategori',
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        KategoriBop::create([
            'nama_kategori' => $request->nama_kategori,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kategori-bop.index')
                         ->with('success', 'Kategori BOP berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kategori = KategoriBop::findOrFail($id);
        return view('master.kategori-bop.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriBop::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_bop,nama_kategori,' . $id . ',id_kategori_bop',
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kategori-bop.index')
                         ->with('success', 'Kategori BOP berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kategori = KategoriBop::findOrFail($id);

        // Cek jika kategori sudah terhubung dengan transaksi BOP
        if ($kategori->biayaOverheadPabrik()->exists()) {
            return redirect()->route('kategori-bop.index')
                             ->with('error', 'Kategori ini tidak dapat dihapus karena telah digunakan dalam transaksi BOP.');
        }

        $kategori->delete();

        return redirect()->route('kategori-bop.index')
                         ->with('success', 'Kategori BOP berhasil dihapus!');
    }
}
