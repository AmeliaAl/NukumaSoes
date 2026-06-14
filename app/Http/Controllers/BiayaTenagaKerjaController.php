<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiayaTenagaKerja;
use App\Models\PermintaanProduksi;
use App\Models\TenagaKerja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BiayaTenagaKerjaController extends Controller
{
    public function index()
    {
        $biayaTenagaKerja = BiayaTenagaKerja::whereHas('tenagaKerja', function ($query) {
                                      $query->where('jenis_tenaga', 'langsung');
                                  })
                                  ->with(['permintaanProduksi.produk', 'tenagaKerja', 'admin'])
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return view('transaksi.biaya-tenaga-kerja.index', compact('biayaTenagaKerja'));
    }

    public function create()
    {
        return redirect()->route('kehadiran.index')
            ->with('error', 'Pencatatan Biaya Tenaga Kerja secara manual telah dinonaktifkan. Seluruh biaya tenaga kerja sekarang dialokasikan secara otomatis berdasarkan Absensi Harian.');
    }

    public function store(Request $request)
    {
        return redirect()->route('kehadiran.index')
            ->with('error', 'Pencatatan Biaya Tenaga Kerja secara manual telah dinonaktifkan. Seluruh biaya tenaga kerja sekarang dialokasikan secara otomatis berdasarkan Absensi Harian.');
    }

    public function edit($id)
    {
        return redirect()->route('kehadiran.index')
            ->with('error', 'Pengeditan Biaya Tenaga Kerja secara manual telah dinonaktifkan. Seluruh perubahan data dapat disesuaikan kembali melalui Absensi Harian.');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('kehadiran.index')
            ->with('error', 'Pengeditan Biaya Tenaga Kerja secara manual telah dinonaktifkan. Seluruh perubahan data dapat disesuaikan kembali melalui Absensi Harian.');
    }

    public function destroy($id)
    {
        return redirect()->route('kehadiran.index')
            ->with('error', 'Penghapusan Biaya Tenaga Kerja secara manual telah dinonaktifkan. Seluruh data disesuaikan otomatis melalui Absensi Harian.');
    }
}