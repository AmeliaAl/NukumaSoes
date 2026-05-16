<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengeluaranEntry;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pengeluaran = PengeluaranEntry::all();
        $expiredHistories = \App\Models\ExpiredProductHistory::orderBy('created_at', 'desc')->get();
        return view('pengeluaran.index', compact('pengeluaran', 'expiredHistories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coas = \App\Models\Coa::all();
        $expiredProducts = \App\Models\Inventory::where('tgl_expired', '<=', now()->toDateString())->get();
        
        // Enrich expired products with cost data from PersediaanEntry
        $expiredProducts->transform(function($product) {
            $entry = \App\Models\PersediaanEntry::where('kode_produk', $product->kode_produk)
                ->where('no_batch', $product->no_batch)
                ->first();
            
            $product->harga_pokok_per_pack = 0;
            if ($entry && $entry->jumlah_pack > 0) {
                $product->harga_pokok_per_pack = $entry->total_harga / $entry->jumlah_pack;
            }
            return $product;
        });

        // Fetch cost history from ProdukKeluarEntry for the datalist
        $costHistory = \App\Models\ProdukKeluarEntry::whereNotNull('harga_pokok_per_pack')
            ->where('harga_pokok_per_pack', '>', 0)
            ->distinct()
            ->orderBy('harga_pokok_per_pack', 'desc')
            ->pluck('harga_pokok_per_pack');

        return view('pengeluaran.create', compact('coas', 'expiredProducts', 'costHistory'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_akun' => 'nullable|string|max:255',
            'tanggal_pengeluaran' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'jumlah_expired' => 'nullable|integer|min:0',
            'harga_pokok_per_pack' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        if (!$request->nama_akun) {
            $data['nama_akun'] = $request->produk_expired ? 'Kerugian Produk Expired' : 'Biaya Lain-lain';
        }
        $data['deskripsi'] = '-';
        $pengeluaran = PengeluaranEntry::create($data);

        // Determine Journal Details
        if ($request->produk_expired) {
            $keteranganDebit = 'Kerugian Produk Expired';
            $keteranganKredit = 'Persediaan Produk Jadi';
            
            $coaDebit = \App\Models\Coa::where('nama_akun', 'LIKE', '%Kerugian Produk Expired%')->first();
            $refDebit = $coaDebit ? $coaDebit->kode_akun : '515'; // Default for loss

            $coaKredit = \App\Models\Coa::where('nama_akun', 'LIKE', '%Persediaan Produk Jadi%')->first();
            $refKredit = $coaKredit ? $coaKredit->kode_akun : '113'; // Default for inventory
        } else {
            $keteranganDebit = $data['nama_akun'];
            $keteranganKredit = 'Kas';

            $coaDebit = \App\Models\Coa::where('nama_akun', $data['nama_akun'])->first();
            $refDebit = $coaDebit ? $coaDebit->kode_akun : '-';

            $coaKas = \App\Models\Coa::where('nama_akun', 'LIKE', '%Kas%')->where('nama_akun', 'NOT LIKE', '%Biaya%')->first();
            $refKredit = $coaKas ? $coaKas->kode_akun : '111';
        }

        // Create Jurnal Umum
        \App\Models\JurnalUmum::create([
            'tanggal' => $request->tanggal_pengeluaran,
            'keterangan' => $keteranganDebit,
            'ref' => $refDebit,
            'debit' => $request->nominal,
            'kredit' => 0,
        ]);
        \App\Models\JurnalUmum::create([
            'tanggal' => $request->tanggal_pengeluaran,
            'keterangan' => $keteranganKredit,
            'ref' => $refKredit,
            'debit' => 0,
            'kredit' => $request->nominal,
        ]);

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengeluaran = PengeluaranEntry::findOrFail($id);
        $coas = \App\Models\Coa::all();
        
        // Fetch cost history for the datalist
        $costHistory = \App\Models\ProdukKeluarEntry::whereNotNull('harga_pokok_per_pack')
            ->where('harga_pokok_per_pack', '>', 0)
            ->distinct()
            ->orderBy('harga_pokok_per_pack', 'desc')
            ->pluck('harga_pokok_per_pack');

        return view('pengeluaran.edit', compact('pengeluaran', 'coas', 'costHistory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_akun' => 'nullable|string|max:255',
            'tanggal_pengeluaran' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'jumlah_expired' => 'nullable|integer|min:0',
            'harga_pokok_per_pack' => 'nullable|numeric|min:0',
        ]);

        $pengeluaran = PengeluaranEntry::findOrFail($id);
        $data = $request->all();
        if (!$request->nama_akun) {
            $data['nama_akun'] = $request->produk_expired ? 'Kerugian Produk Expired' : 'Biaya Lain-lain';
        }
        $data['deskripsi'] = '-';
        $pengeluaran->update($data);

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengeluaran = PengeluaranEntry::findOrFail($id);
        $pengeluaran->delete();

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
