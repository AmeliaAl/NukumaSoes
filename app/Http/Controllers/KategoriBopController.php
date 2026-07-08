<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriBop;
use App\Models\Akun;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KategoriBopController extends Controller
{
    private const BOP_ACCOUNT_CODES = ['601', '612', '613', '616', '619', '620', '653', '712', '713', '535'];

    public function index()
    {
        $kategoriBop = KategoriBop::productionScope()->with('akun')->withCount('biayaOverheadPabrik')->get();
        return view('master.kategori-bop.index', compact('kategoriBop'));
    }

    public function create()
    {
        $akunBop = $this->akunBop();
        return view('master.kategori-bop.create', compact('akunBop'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_bop,nama_kategori',
            'id_akun' => ['required', Rule::exists('akun', 'id_akun')->where(fn ($q) => $q->whereIn('kode_akun', self::BOP_ACCOUNT_CODES)->where('status', 'aktif'))],
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        $this->ensureProductionCategory($request->nama_kategori);

        KategoriBop::create([
            'nama_kategori' => $request->nama_kategori,
            'id_akun' => $request->id_akun,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kategori-bop.index')
                         ->with('success', 'Kategori BOP berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kategori = KategoriBop::findOrFail($id);
        $akunBop = $this->akunBop();
        return view('master.kategori-bop.edit', compact('kategori', 'akunBop'));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriBop::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_bop,nama_kategori,' . $id . ',id_kategori_bop',
            'id_akun' => ['required', Rule::exists('akun', 'id_akun')->where(fn ($q) => $q->whereIn('kode_akun', self::BOP_ACCOUNT_CODES)->where('status', 'aktif'))],
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori harus diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        $this->ensureProductionCategory($request->nama_kategori);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'id_akun' => $request->id_akun,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kategori-bop.index')
                         ->with('success', 'Kategori BOP berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kategori = KategoriBop::findOrFail($id);

        // Cek jika kategori sudah terhubung dengan transaksi BOP
        if ($kategori->biayaOverheadPabrik()->exists()) {
            return redirect()->route('kategori-bop.index')
                             ->with('error', 'Kategori ini tidak dapat dihapus karena telah digunakan dalam transaksi BOP.');
        }

        $kategori->delete();

        return redirect()->route('kategori-bop.index')
                         ->with('success', 'Kategori BOP berhasil dihapus!');
    }

    private function akunBop()
    {
        return Akun::whereIn('kode_akun', self::BOP_ACCOUNT_CODES)
            ->aktif()
            ->orderBy('kode_akun')
            ->get();
    }

    private function ensureProductionCategory(string $name): void
    {
        if (! KategoriBop::isAssetRelatedName($name)) {
            return;
        }

        throw ValidationException::withMessages([
            'nama_kategori' => 'Kategori BOP terkait mesin/aset tidak masuk lingkup aplikasi produksi. Catat biaya operasionalnya saja, misalnya Gas, Listrik, atau Air, lalu jelaskan mesin yang digunakan pada keterangan.',
        ]);
    }
}
