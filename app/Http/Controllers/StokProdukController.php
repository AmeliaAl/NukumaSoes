<?php

namespace App\Http\Controllers;

use App\Models\StokProduk;
use Illuminate\Http\Request;

class StokProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = StokProduk::with(['produk', 'permintaanProduksi'])
            ->orderBy('tanggal_masuk', 'desc')
            ->orderBy('id_stok_produk', 'desc');

        // Filter by type
        if ($request->has('tipe_stok') && $request->tipe_stok != '') {
            $query->where('tipe_stok', $request->tipe_stok);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $stokProduk = $query->paginate(15);

        // Calculate summary
        $totalWip = StokProduk::wip()->tersedia()->sum('jumlah');
        $totalJadi = StokProduk::barangJadi()->tersedia()->sum('jumlah');
        $nilaiWip = StokProduk::wip()->tersedia()->sum('total_nilai');
        $nilaiJadi = StokProduk::barangJadi()->tersedia()->sum('total_nilai');

        return view('transaksi.stok-produk.index', compact(
            'stokProduk', 
            'totalWip', 
            'totalJadi', 
            'nilaiWip', 
            'nilaiJadi'
        ));
    }

    public function show($id)
    {
        $stok = StokProduk::with(['produk', 'permintaanProduksi'])->findOrFail($id);
        return view('transaksi.stok-produk.show', compact('stok'));
    }

    public function destroy($id)
    {
        $stok = StokProduk::findOrFail($id);
        $stok->delete();

        return redirect()->route('stok-produk.index')
            ->with('success', 'Data stok produk berhasil dihapus');
    }
}
