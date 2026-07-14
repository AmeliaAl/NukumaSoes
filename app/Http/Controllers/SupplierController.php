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
        $this->validateSupplier($request);

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
        $this->validateSupplier($request);

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

    /**
     * Validasi field telepon dengan pesan error spesifik per kondisi.
     */
    private function validateSupplier(Request $request): void
    {
        // Tahap 1: cek hanya angka
        $request->validate(
            ['telepon' => ['required', 'regex:/^[0-9]+$/']],
            ['telepon.regex' => 'Nomor telepon hanya boleh terdiri dari angka.']
        );

        // Tahap 2: cek awalan 08
        $request->validate(
            ['telepon' => ['regex:/^08/']],
            ['telepon.regex' => 'Nomor telepon harus diawali dengan 08.']
        );

        // Tahap 3: cek panjang digit dan field lainnya
        $request->validate(
            [
                'nama_supplier' => 'required',
                'alamat'        => 'required',
                'telepon'       => ['digits_between:10,13'],
            ],
            ['telepon.digits_between' => 'Nomor telepon harus terdiri dari 10–13 digit.']
        );
    }
}