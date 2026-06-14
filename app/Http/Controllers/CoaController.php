<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Imports\CoaImport;
use App\Models\Coa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
=======
use App\Models\coa;
use App\Http\Requests\StorecoaRequest;
use App\Http\Requests\UpdatecoaRequest;
>>>>>>> origin/sarah-backup-final

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
<<<<<<< HEAD
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
=======
    public function index(){
        $coa = Coa::all();
        return view('coa/view',
                        [ 
                            'coa'=>$coa,
                            'title'=>'contoh m2',
                            'nama'=>'Sarah Al Arroya'
                        ]
                    ); 
>>>>>>> origin/sarah-backup-final
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
<<<<<<< HEAD
        return view('coa.create');
=======
        //
>>>>>>> origin/sarah-backup-final
    }

    /**
     * Store a newly created resource in storage.
     */
<<<<<<< HEAD
    public function store(Request $request)
    {
        $request->validate([
            'kode_akun' => 'nullable|string|max:255|unique:coas,kode_akun',
            'header_akun' => 'nullable|string|max:255',
            'nama_akun' => 'nullable|string|max:255',
        ]);

        Coa::create($request->all());

        return redirect()->route('coa.index')->with('success', 'COA berhasil ditambahkan.');
=======
    public function store(StorecoaRequest $request)
    {
        //
>>>>>>> origin/sarah-backup-final
    }

    /**
     * Display the specified resource.
     */
<<<<<<< HEAD
    public function show(string $id)
    {
        $coa = Coa::findOrFail($id);
        return view('coa.show', compact('coa'));
=======
    public function show(coa $coa)
    {
        //
>>>>>>> origin/sarah-backup-final
    }

    /**
     * Show the form for editing the specified resource.
     */
<<<<<<< HEAD
    public function edit(string $id)
    {
        $coa = Coa::findOrFail($id);
        return view('coa.edit', compact('coa'));
=======
    public function edit(coa $coa)
    {
        //
>>>>>>> origin/sarah-backup-final
    }

    /**
     * Update the specified resource in storage.
     */
<<<<<<< HEAD
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_akun' => 'required|string|max:255|unique:coas,kode_akun,' . $id,
            'nama_akun' => 'required|string|max:255',
        ]);

        $coa = Coa::findOrFail($id);
        $coa->update($request->all());

        return redirect()->route('coa.index')->with('success', 'COA berhasil diperbarui.');
=======
    public function update(UpdatecoaRequest $request, coa $coa)
    {
        //
>>>>>>> origin/sarah-backup-final
    }

    /**
     * Remove the specified resource from storage.
     */
<<<<<<< HEAD
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
        ]);

        try {
            Excel::import(new CoaImport, $request->file('file'));
            return redirect()->route('coa.index')->with('success', 'COA berhasil diimpor dari Excel!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(function ($failure) {
                return implode(', ', $failure->errors());
            })->implode('<br>');
            return redirect()->back()->with('error', 'Validasi gagal: ' . $errors);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
=======
    public function destroy(coa $coa)
    {
        //
>>>>>>> origin/sarah-backup-final
    }
}
