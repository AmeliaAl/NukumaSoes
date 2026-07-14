<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index()
    {
        $bahanBakus = BahanBaku::all();

        return view('bahan_baku.index', compact('bahanBakus'));
    }

    public function create()
    {
        return view('bahan_baku.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan'      => 'required|string|max:255|unique:bahan_baku,nama_bahan',
            'isi_per_kemasan' => 'nullable|string|max:255',
            'satuan'          => 'required|string|max:255',
            'jenis_bahan'     => 'required|in:Langsung,Tidak Langsung',
        ], [
            'nama_bahan.required'  => 'Nama Bahan wajib diisi.',
            'nama_bahan.unique'    => 'Nama Bahan sudah terdaftar. Tidak dapat menginput data yang sama dua kali.',
            'satuan.required'      => 'Satuan wajib diisi.',
            'jenis_bahan.required' => 'Jenis Bahan wajib dipilih.',
            'jenis_bahan.in'       => 'Jenis Bahan tidak valid.',
        ]);

        // Generate kode bahan otomatis
        $lastBahan = BahanBaku::latest('id')->first();
        $newNumber = $lastBahan ? ((int) substr($lastBahan->kode_bahan, 3)) + 1 : 1;
        $kodeBahan = 'BB-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        BahanBaku::create([
            'kode_bahan'      => $kodeBahan,
            'nama_bahan'      => $request->nama_bahan,
            'jenis_bahan'     => $request->jenis_bahan,
            'satuan'          => $request->satuan,
            'isi_per_kemasan' => $request->isi_per_kemasan,
            'stok_minimum'    => 0,
            'stok_saat_ini'   => 0,
        ]);

        return redirect('/bahan-baku');
    }

    public function show(BahanBaku $bahanBaku)
    {
        //
    }

    public function edit(BahanBaku $bahanBaku)
    {
        return view('bahan_baku.edit', compact('bahanBaku'));
    }

    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $request->validate([
            // unique kecuali record sendiri
            'nama_bahan'      => 'required|string|max:255|unique:bahan_baku,nama_bahan,' . $bahanBaku->id,
            'isi_per_kemasan' => 'nullable|string|max:255',
            'satuan'          => 'required|string|max:255',
            'jenis_bahan'     => 'required|in:Langsung,Tidak Langsung',
        ], [
            'nama_bahan.required'  => 'Nama Bahan wajib diisi.',
            'nama_bahan.unique'    => 'Nama Bahan sudah terdaftar. Tidak dapat menginput data yang sama dua kali.',
            'satuan.required'      => 'Satuan wajib diisi.',
            'jenis_bahan.required' => 'Jenis Bahan wajib dipilih.',
            'jenis_bahan.in'       => 'Jenis Bahan tidak valid.',
        ]);

        $bahanBaku->update([
            'nama_bahan'      => $request->nama_bahan,
            'jenis_bahan'     => $request->jenis_bahan,
            'satuan'          => $request->satuan,
            'isi_per_kemasan' => $request->isi_per_kemasan,
        ]);

        return redirect('/bahan-baku');
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();

        return redirect('/bahan-baku');
    }
}
