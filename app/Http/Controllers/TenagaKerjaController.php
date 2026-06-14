<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TenagaKerja;

class TenagaKerjaController extends Controller
{
    public function index()
    {
        $tenagaKerja = TenagaKerja::all();
        return view('master.tenaga-kerja.index', compact('tenagaKerja'));
    }

    public function create()
    {
        // Generate kode otomatis
        $lastTenaga = TenagaKerja::orderBy('kode_tenaga', 'desc')->first();
        
        if ($lastTenaga) {
            $lastNumber = (int) substr($lastTenaga->kode_tenaga, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $kode_tenaga = 'TK-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        return view('master.tenaga-kerja.create', compact('kode_tenaga'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_tenaga' => 'required|string|max:20|unique:tenaga_kerja,kode_tenaga',
            'nama_tenaga' => 'required|string|max:100',
            'jabatan' => 'required|string|max:50',
            'jenis_tenaga' => 'required|in:langsung,tidak_langsung',
            'bagian' => 'nullable|string|max:50',
            'upah_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'kode_tenaga.required' => 'Kode tenaga harus diisi',
            'kode_tenaga.unique' => 'Kode tenaga sudah digunakan',
            'nama_tenaga.required' => 'Nama tenaga harus diisi',
            'jabatan.required' => 'Jabatan harus diisi',
            'jenis_tenaga.required' => 'Jenis tenaga kerja harus dipilih',
            'jenis_tenaga.in' => 'Jenis tenaga kerja tidak valid',
            'upah_per_jam.required' => 'Upah per jam harus diisi',
            'upah_per_jam.numeric' => 'Upah per jam harus berupa angka',
            'upah_per_jam.min' => 'Upah per jam tidak boleh kurang dari 0',
        ]);

        TenagaKerja::create([
            'kode_tenaga' => $request->kode_tenaga,
            'nama_tenaga' => $request->nama_tenaga,
            'jabatan' => $request->jabatan,
            'bagian' => $request->bagian,
            'jenis_tenaga' => $request->jenis_tenaga,
            'upah_per_jam' => $request->upah_per_jam,
            'status' => $request->status,
        ]);

        return redirect()->route('tenaga-kerja.index')
                       ->with('success', 'Tenaga kerja berhasil ditambahkan!');
    }

    public function show($id)
    {
        $tenaga = TenagaKerja::findOrFail($id);
        return view('master.tenaga-kerja.show', compact('tenaga'));
    }

    public function edit($id)
    {
        $tenaga = TenagaKerja::findOrFail($id);
        return view('master.tenaga-kerja.edit', compact('tenaga'));
    }

    public function update(Request $request, $id)
    {
        $tenaga = TenagaKerja::findOrFail($id);

        $request->validate([
            'nama_tenaga' => 'required|string|max:100',
            'jabatan' => 'required|string|max:50',
            'bagian' => 'nullable|string|max:50',
            'jenis_tenaga' => 'required|in:langsung,tidak_langsung',
            'upah_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_tenaga.required' => 'Nama tenaga harus diisi',
            'jabatan.required' => 'Jabatan harus diisi',
            'jenis_tenaga.required' => 'Jenis tenaga kerja harus dipilih',
            'upah_per_jam.required' => 'Upah per jam harus diisi',
        ]);

        $tenaga->update([
            'nama_tenaga' => $request->nama_tenaga,
            'jabatan' => $request->jabatan,
            'bagian' => $request->bagian,
            'jenis_tenaga' => $request->jenis_tenaga,
            'upah_per_jam' => $request->upah_per_jam,
            'status' => $request->status,
        ]);

        return redirect()->route('tenaga-kerja.index')
                       ->with('success', 'Tenaga kerja berhasil diupdate!');
    }

    public function destroy($id)
    {
        $tenaga = TenagaKerja::findOrFail($id);

        // Cek apakah ada transaksi terkait
        if ($tenaga->biayaTenagaKerja()->count() > 0) {
            return back()->with('error', 'Tenaga kerja tidak dapat dihapus karena sudah ada transaksi terkait!');
        }

        $tenaga->delete();

        return redirect()->route('tenaga-kerja.index')
                       ->with('success', 'Tenaga kerja berhasil dihapus!');
    }
}