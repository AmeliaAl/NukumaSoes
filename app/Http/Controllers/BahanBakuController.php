<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\StokBahanBaku;
use App\Models\PemakaianBahanBaku;

class BahanBakuController extends Controller
{
    public function index()
    {
        $bahanBaku = BahanBaku::all();
        return view('master.bahan-baku.index', compact('bahanBaku'));
    }

    public function create()
    {
        // Generate kode otomatis
        $lastBahan = BahanBaku::orderBy('kode_bahan', 'desc')->first();
        
        if ($lastBahan) {
            $lastNumber = (int) substr($lastBahan->kode_bahan, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $kode_bahan = 'BB-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        return view('master.bahan-baku.create', compact('kode_bahan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_bahan' => 'required|string|max:20|unique:bahan_baku,kode_bahan',
            'nama_bahan' => 'required|string|max:100',
            'jenis_bahan' => 'required|in:langsung,tidak_langsung',
            'satuan' => 'required|string|max:20',
            'satuan_beli' => 'nullable|string|max:20',
            'isi_per_kemasan' => 'nullable|numeric|min:0.01',
            'stok_minimum' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'keterangan' => 'nullable|string',
        ], [
            'kode_bahan.required' => 'Kode bahan harus diisi',
            'kode_bahan.unique' => 'Kode bahan sudah digunakan',
            'nama_bahan.required' => 'Nama bahan harus diisi',
            'jenis_bahan.required' => 'Jenis bahan harus diisi',
            'satuan.required' => 'Satuan harus dipilih',
            'stok_minimum.required' => 'Stok minimum harus diisi',
            'stok_minimum.numeric' => 'Stok minimum harus berupa angka',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0',
        ]);

        BahanBaku::create([
            'kode_bahan' => $request->kode_bahan,
            'nama_bahan' => $request->nama_bahan,
            'jenis_bahan' => $request->jenis_bahan,
            'satuan' => $request->satuan,
            'satuan_beli' => $request->satuan_beli,
            'isi_per_kemasan' => $request->isi_per_kemasan ?? 1,
            'stok_saat_ini' => 0,
            'stok_minimum' => $request->stok_minimum,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('bahan-baku.index')
                       ->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function show($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        
        // Stok batches (FIFO)
        $stokBatches = StokBahanBaku::where('id_bahan', $id)
                                   ->where('sisa_stok', '>', 0)
                                   ->orderBy('tanggal_masuk', 'asc')
                                   ->get();
        
        // Riwayat penggunaan (10 terakhir)
        $riwayatPenggunaan = PemakaianBahanBaku::with(['permintaanProduksi.produk'])
                                              ->where('id_bahan', $id)
                                              ->orderBy('created_at', 'desc')
                                              ->take(10)
                                              ->get();
        
        return view('master.bahan-baku.show', compact('bahan', 'stokBatches', 'riwayatPenggunaan'));
    }

    public function edit($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        return view('master.bahan-baku.edit', compact('bahan'));
    }

    public function update(Request $request, $id)
    {
        $bahan = BahanBaku::findOrFail($id);

        $request->validate([
            'nama_bahan' => 'required|string|max:100',
            'jenis_bahan' => 'required|in:langsung,tidak_langsung',
            'satuan' => 'required|string|max:20',
            'satuan_beli' => 'nullable|string|max:20',
            'isi_per_kemasan' => 'nullable|numeric|min:0.01',
            'stok_minimum' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'keterangan' => 'nullable|string',
        ], [
            'nama_bahan.required' => 'Nama bahan harus diisi',
            'jenis_bahan.required' => 'Jenis bahan harus diisi',
            'satuan.required' => 'Satuan harus dipilih',
            'stok_minimum.required' => 'Stok minimum harus diisi',
        ]);

        $bahan->update([
            'nama_bahan' => $request->nama_bahan,
            'jenis_bahan' => $request->jenis_bahan,
            'satuan' => $request->satuan,
            'satuan_beli' => $request->satuan_beli,
            'isi_per_kemasan' => $request->isi_per_kemasan ?? 1,
            'stok_minimum' => $request->stok_minimum,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('bahan-baku.index')
                       ->with('success', 'Bahan baku berhasil diupdate!');
    }

    public function destroy($id)
    {
        $bahan = BahanBaku::findOrFail($id);

        // Cek apakah ada transaksi terkait
        if ($bahan->stokBahanBaku()->count() > 0 || $bahan->pemakaianBahanBaku()->count() > 0) {
            return back()->with('error', 'Bahan baku tidak dapat dihapus karena sudah ada transaksi terkait!');
        }

        $bahan->delete();

        return redirect()->route('bahan-baku.index')
                       ->with('success', 'Bahan baku berhasil dihapus!');
    }

    public function kartuStok($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        
        // Get all stock movements (masuk & keluar)
        $stokMasuk = StokBahanBaku::where('id_bahan', $id)
                                 ->select('tanggal_masuk as tanggal', 'jumlah_masuk as jumlah', 'harga_per_satuan', 'keterangan')
                                 ->selectRaw("'masuk' as jenis")
                                 ->get();
        
        $stokKeluar = PemakaianBahanBaku::where('id_bahan', $id)
                                       ->select('created_at as tanggal', 'jumlah_pakai as jumlah', 'harga_per_satuan')
                                       ->selectRaw("'keluar' as jenis")
                                       ->selectRaw("CONCAT('Job Order #', permintaan_produksi.nomor_job) as keterangan")
                                       ->join('permintaan_produksi', 'pemakaian_bahan_baku.id_permintaan_produksi', '=', 'permintaan_produksi.id_permintaan_produksi')
                                       ->get();
        
        // Merge and sort by date
        $kartuStok = $stokMasuk->concat($stokKeluar)->sortBy('tanggal');
        
        // Calculate running balance
        $saldo = 0;
        $kartuStok = $kartuStok->map(function($item) use (&$saldo) {
            if ($item->jenis == 'masuk') {
                $saldo += $item->jumlah;
            } else {
                $saldo -= $item->jumlah;
            }
            $item->saldo = $saldo;
            return $item;
        });
        
        return view('master.bahan-baku.kartu-stok', compact('bahan', 'kartuStok'));
    }

    public function getStokFifo($id)
    {
        $stokBatches = StokBahanBaku::where('id_bahan', $id)
                                   ->where('status', 'tersedia')
                                   ->where('sisa_stok', '>', 0)
                                   ->orderBy('tanggal_masuk', 'asc')
                                   ->get();
        
        return response()->json([
            'success' => true,
            'data' => $stokBatches
        ]);
    }
}