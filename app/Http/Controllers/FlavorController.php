<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FlavorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('flavors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_rasa' => 'required|string|max:255|unique:flavors,nama_rasa',
        ]);

        \App\Models\Flavor::create($request->all());

        return redirect()->route('kategori.index')->with('success', 'Varian rasa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rasa = \App\Models\Flavor::findOrFail($id);
        return view('flavors.edit', compact('rasa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rasa = \App\Models\Flavor::findOrFail($id);
        
        $request->validate([
            'nama_rasa' => 'required|string|max:255|unique:flavors,nama_rasa,' . $id,
        ]);

        $rasa->update($request->all());

        return redirect()->route('kategori.index')->with('success', 'Varian rasa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rasa = \App\Models\Flavor::findOrFail($id);
        $rasa->delete();

        return redirect()->route('kategori.index')->with('success', 'Varian rasa berhasil dihapus.');
    }
}
