<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::all();

        return view('supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'nama_supplier' => 'required',

            'alamat' => 'required',

            'telepon' => [

                'required',
                'regex:/^08[0-9]{8,11}$/'

            ]

        ], [

            'telepon.regex' =>
                'Nomor telepon harus diawali 08 dan terdiri dari 10-13 digit.'

        ]);

        Supplier::create([

            'nama_supplier' => $request->nama_supplier,

            'alamat' => $request->alamat,

            'telepon' => $request->telepon,

        ]);

        return redirect('/supplier');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([

            'nama_supplier' => 'required',

            'alamat' => 'required',

            'telepon' => [

                'required',
                'regex:/^08[0-9]{8,11}$/'

            ]

        ], [

            'telepon.regex' =>
                'Nomor telepon harus diawali 08 dan terdiri dari 10-13 digit.'

        ]);

        $supplier->update([

            'nama_supplier' => $request->nama_supplier,

            'alamat' => $request->alamat,

            'telepon' => $request->telepon,

        ]);

        return redirect('/supplier');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect('/supplier');
    }
}