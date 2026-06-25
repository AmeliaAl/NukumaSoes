<?php

namespace App\Http\Controllers;

use App\Models\coa;
use App\Http\Requests\StorecoaRequest;
use App\Http\Requests\UpdatecoaRequest;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $coa = Coa::all();
        return view('coa/view',
                        [ 
                            'coa'=>$coa,
                            'title'=>'contoh m2',
                            'nama'=>'Sarah Al Arroya'
                        ]
                    ); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorecoaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(coa $coa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(coa $coa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatecoaRequest $request, coa $coa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(coa $coa)
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

        //

    }
}
