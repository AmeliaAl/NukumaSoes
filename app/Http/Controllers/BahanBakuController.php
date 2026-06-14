<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bahanBakus = BahanBaku::all();

        return view('bahan_baku.index', compact('bahanBakus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bahan_baku.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | GENERATE KODE BAHAN OTOMATIS
        |--------------------------------------------------------------------------
        */

        $lastBahan = BahanBaku::latest()->first();

        if ($lastBahan) {

            $lastNumber = (int) substr($lastBahan->kode_bahan, 3);

            $newNumber = $lastNumber + 1;

        } else {

            $newNumber = 1;
        }

        $kodeBahan = 'BB-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA
        |--------------------------------------------------------------------------
        */

        BahanBaku::create([

            'kode_bahan' => $kodeBahan,

            'nama_bahan' => $request->nama_bahan,

            'satuan' => $request->satuan,

        ]);

        return redirect('/bahan-baku');
    }

    /**
     * Display the specified resource.
     */
    public function show(BahanBaku $bahanBaku)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BahanBaku $bahanBaku)
    {
        return view('bahan_baku.edit', compact('bahanBaku'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $bahanBaku->update([

            'nama_bahan' => $request->nama_bahan,

            'satuan' => $request->satuan,

        ]);

        return redirect('/bahan-baku');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();

        return redirect('/bahan-baku');
    }
}