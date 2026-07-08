<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAkunRequest;
use App\Http\Requests\UpdateAkunRequest;
use App\Models\Akun;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    /**
     * Display a listing of all akun (Chart of Accounts).
     */
    public function index()
    {
        $akuns = Akun::aktif()->orderBy('kode_akun', 'asc')->get();
        return view('master.akun.index', compact('akuns'));
    }

    /**
     * Show the form for creating a new akun.
     */
    public function create()
    {
        $tipeAkun = Akun::getTipeAkunList();
        return view('master.akun.create', compact('tipeAkun'));
    }

    /**
     * Store a newly created akun in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_akun'   => 'required|string|max:20|unique:akun,kode_akun',
            'nama_akun'   => 'required|string|max:100',
            'tipe_akun'   => 'required|in:aset,kewajiban,ekuitas,pendapatan,beban',
            'saldo_normal' => 'required|in:debit,kredit',
            'keterangan'  => 'nullable|string',
        ], [
            'kode_akun.unique' => 'Kode akun sudah digunakan.',
        ]);

        Akun::create([
            'kode_akun'    => $request->kode_akun,
            'nama_akun'    => $request->nama_akun,
            'tipe_akun'    => $request->tipe_akun,
            'saldo_normal' => $request->saldo_normal,
            'saldo'        => 0,
            'status'       => 'aktif',
            'keterangan'   => $request->keterangan,
        ]);

        return redirect()->route('akun.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Display the specified akun.
     */
    public function show($id)
    {
        $akun = Akun::findOrFail($id);
        return redirect()->route('akun.edit', $akun->id_akun);
    }

    /**
     * Show the form for editing the specified akun.
     */
    public function edit($id)
    {
        $akun     = Akun::findOrFail($id);
        $tipeAkun = Akun::getTipeAkunList();
        return view('master.akun.edit', compact('akun', 'tipeAkun'));
    }

    /**
     * Update the specified akun in storage.
     */
    public function update(Request $request, $id)
    {
        $akun = Akun::findOrFail($id);

        $request->validate([
            'kode_akun'    => 'required|string|max:20|unique:akun,kode_akun,' . $akun->id_akun . ',id_akun',
            'nama_akun'    => 'required|string|max:100',
            'tipe_akun'    => 'required|in:aset,kewajiban,ekuitas,pendapatan,beban',
            'saldo_normal' => 'required|in:debit,kredit',
            'status'       => 'required|in:aktif,nonaktif',
            'keterangan'   => 'nullable|string',
        ]);

        $akun->update([
            'kode_akun'    => $request->kode_akun,
            'nama_akun'    => $request->nama_akun,
            'tipe_akun'    => $request->tipe_akun,
            'saldo_normal' => $request->saldo_normal,
            'status'       => $request->status,
            'keterangan'   => $request->keterangan,
        ]);

        return redirect()->route('akun.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified akun from storage.
     */
    public function destroy($id)
    {
        $akun = Akun::findOrFail($id);

        // Cek apakah akun masih dipakai di jurnal
        if ($akun->jurnalDetail()->count() > 0) {
            return redirect()->route('akun.index')
                ->with('error', 'Akun tidak dapat dihapus karena masih digunakan dalam transaksi jurnal.');
        }

        $akun->delete();

        return redirect()->route('akun.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
