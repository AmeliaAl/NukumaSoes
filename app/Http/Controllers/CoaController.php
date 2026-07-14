<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function index()
    {
        $coas = Akun::all();

        return view('coa.index', compact('coas'));
    }

    public function create()
    {
        return view('coa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_akun'     => ['required', 'string', 'max:50', 'unique:akun,no_akun'],
            'nama_akun'   => ['required', 'string', 'max:255', 'unique:akun,nama_akun'],
            'header_akun' => ['required', 'string', 'in:Aktiva Lancar,Kewajiban Lancar,Pemakaian Bahan Baku,Overhead Produksi,Biaya Beban Operasional Umum,Ekuitas'],
        ], [
            'no_akun.required'     => 'Kode akun wajib diisi.',
            'no_akun.unique'       => 'Kode akun sudah digunakan. Silakan gunakan kode akun yang berbeda.',
            'nama_akun.required'   => 'Nama akun wajib diisi.',
            'nama_akun.unique'     => 'Nama akun sudah tersedia. Silakan gunakan nama akun lain.',
            'header_akun.required' => 'Kelompok akun wajib dipilih.',
            'header_akun.in'       => 'Kelompok akun tidak valid.',
        ]);

        Akun::create([
            'no_akun'     => $request->no_akun,
            'nama_akun'   => $request->nama_akun,
            'header_akun' => $request->header_akun,
        ]);

        return redirect('/coa')->with('success', 'Data akun berhasil disimpan.');
    }

    public function show(Akun $coa)
    {
        //
    }

    public function edit(Akun $coa)
    {
        return view('coa.edit', compact('coa'));
    }

    public function update(Request $request, Akun $coa)
    {
        $request->validate([
            'no_akun'     => ['required', 'string', 'max:50', 'unique:akun,no_akun,' . $coa->id],
            'nama_akun'   => ['required', 'string', 'max:255', 'unique:akun,nama_akun,' . $coa->id],
            'header_akun' => ['required', 'string', 'in:Aktiva Lancar,Kewajiban Lancar,Pemakaian Bahan Baku,Overhead Produksi,Biaya Beban Operasional Umum,Ekuitas'],
        ], [
            'no_akun.required'     => 'Kode akun wajib diisi.',
            'no_akun.unique'       => 'Kode akun sudah digunakan. Silakan gunakan kode akun yang berbeda.',
            'nama_akun.required'   => 'Nama akun wajib diisi.',
            'nama_akun.unique'     => 'Nama akun sudah tersedia. Silakan gunakan nama akun lain.',
            'header_akun.required' => 'Kelompok akun wajib dipilih.',
            'header_akun.in'       => 'Kelompok akun tidak valid.',
        ]);

        $coa->update([
            'no_akun'     => $request->no_akun,
            'nama_akun'   => $request->nama_akun,
            'header_akun' => $request->header_akun,
        ]);

        return redirect('/coa')->with('success', 'Data akun berhasil diperbarui.');
    }

    public function destroy(Akun $coa)
    {
        $coa->delete();

        return redirect('/coa');
    }
}
