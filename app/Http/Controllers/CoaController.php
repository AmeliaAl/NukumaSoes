<?php

namespace App\Http\Controllers;

use App\Imports\CoaImport;
use App\Models\Coa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kodeAkunSearch = $request->get('kode_akun_search');
        $namaAkunSearch = $request->get('nama_akun_search');

        $coas = Coa::when($kodeAkunSearch, function ($query, $kodeAkunSearch) {
            return $query->where('kode_akun', 'like', '%' . $kodeAkunSearch . '%');
        })
        ->when($namaAkunSearch, function ($query, $namaAkunSearch) {
            return $query->where('nama_akun', 'like', '%' . $namaAkunSearch . '%');
        })
        ->orderBy('kode_akun', 'asc')
        ->get();

        return view('coa.index', compact('coas', 'kodeAkunSearch', 'namaAkunSearch'));
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('coa.import');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_akun' => 'nullable|string|max:255|unique:coas,kode_akun',
            'header_akun' => 'nullable|string|max:255',
            'nama_akun' => 'nullable|string|max:255',
        ]);

        Coa::create($request->all());

        return redirect()->route('coa.index')->with('success', 'COA berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coa = Coa::findOrFail($id);
        return view('coa.show', compact('coa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $coa = Coa::findOrFail($id);
        return view('coa.edit', compact('coa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_akun' => 'required|string|max:255|unique:coas,kode_akun,' . $id,
            'nama_akun' => 'required|string|max:255',
        ]);

        $coa = Coa::findOrFail($id);
        $coa->update($request->all());

        return redirect()->route('coa.index')->with('success', 'COA berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $coa = Coa::findOrFail($id);
        $coa->delete();

        return redirect()->route('coa.index')->with('success', 'COA berhasil dihapus.');
    }

    /**
     * Import COA from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048', // 2MB max
            'hapus_lama' => 'nullable|boolean',
        ]);

        $removeOld = $request->boolean('hapus_lama');

        try {
            if ($removeOld) {
                Coa::truncate();
            }

            Excel::import(new CoaImport, $request->file('file'));

            $message = $removeOld
                ? 'COA lama berhasil dihapus dan data baru berhasil diimpor dari Excel!'
                : 'COA berhasil diimpor dari Excel!';

            return redirect()->route('coa.index')->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(function ($failure) {
                return implode(', ', $failure->errors());
            })->implode('<br>');
            return redirect()->back()->with('error', 'Validasi gagal: ' . $errors);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
