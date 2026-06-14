<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\BomBahan;
use App\Models\BomMesin;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::with(['permintaanProduksi', 'bomBahan', 'bomMesin'])->get();
        return view('master.produk.index', compact('produk'));
    }

    public function create()
    {
        // Generate kode otomatis
        $lastProduk = Produk::orderBy('kode_produk', 'desc')->first();
        
        if ($lastProduk) {
            $lastNumber = (int) substr($lastProduk->kode_produk, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $kode_produk = 'PRD-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        $bahanBaku = BahanBaku::where('status', 'aktif')->orderBy('nama_bahan')->get();
        $wipProduk = Produk::where('status', 'aktif')->whereIn('tipe_produk', ['kulit', 'isi'])->orderBy('nama_produk')->get();
        
        $bomOptions = collect();
        foreach ($bahanBaku as $b) {
            $bomOptions->push([
                'id' => 'bahan_' . $b->id_bahan,
                'nama' => $b->nama_bahan . ' (Bahan Baku)',
                'kode' => $b->kode_bahan,
                'satuan' => $b->satuan,
            ]);
        }
        foreach ($wipProduk as $w) {
            $bomOptions->push([
                'id' => 'wip_' . $w->id_produk,
                'nama' => $w->nama_produk . ' (WIP - ' . ucfirst($w->tipe_produk) . ')',
                'kode' => $w->kode_produk,
                'satuan' => $w->satuan_produk,
            ]);
        }
        
        $bahanBakuJson = $bomOptions->toJson();
        
        return view('master.produk.create', compact('kode_produk', 'bahanBakuJson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|string|max:20|unique:produk,kode_produk',
            'nama_produk' => 'required|string|max:100',
            'tipe_produk' => 'required|in:kulit,isi,jadi',
            'satuan_produk' => 'required|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'nullable|string',
            // BOM Bahan
            'bom_bahan_id.*' => 'nullable|string',
            'bom_bahan_keterangan.*' => 'nullable|string',
            // BOM Mesin
            'bom_mesin_nama.*' => 'nullable|string|max:100',
            'bom_mesin_keterangan.*' => 'nullable|string',
        ], [
            'kode_produk.required' => 'Kode produk harus diisi',
            'kode_produk.unique' => 'Kode produk sudah digunakan',
            'nama_produk.required' => 'Nama produk harus diisi',
            'tipe_produk.required' => 'Tipe produk harus dipilih',
            'satuan_produk.required' => 'Satuan produk harus dipilih',
        ]);

        DB::beginTransaction();
        try {
            $produk = Produk::create([
                'kode_produk' => $request->kode_produk,
                'nama_produk' => $request->nama_produk,
                'tipe_produk' => $request->tipe_produk,
                'satuan_produk' => $request->satuan_produk,
                'status' => $request->status,
                'deskripsi' => $request->deskripsi,
            ]);

            // Simpan BOM Bahan
            if ($request->bom_bahan_id) {
                foreach ($request->bom_bahan_id as $i => $prefixedId) {
                    if ($prefixedId) {
                        $parts = explode('_', $prefixedId);
                        if (count($parts) === 2) {
                            $type = $parts[0];
                            $idVal = $parts[1];
                            
                            $bomData = [
                                'id_produk' => $produk->id_produk,
                                'jumlah_kebutuhan' => 0.00,
                                'keterangan' => $request->bom_bahan_keterangan[$i] ?? null,
                            ];
                            
                            if ($type === 'bahan') {
                                $bomData['id_bahan'] = $idVal;
                                $bomData['id_produk_wip'] = null;
                            } elseif ($type === 'wip') {
                                $bomData['id_bahan'] = null;
                                $bomData['id_produk_wip'] = $idVal;
                            }
                            
                            BomBahan::create($bomData);
                        }
                    }
                }
            }

            // Simpan BOM Mesin
            if ($request->bom_mesin_nama) {
                foreach ($request->bom_mesin_nama as $i => $namaMesin) {
                    if ($namaMesin) {
                        BomMesin::create([
                            'id_produk' => $produk->id_produk,
                            'nama_mesin' => $namaMesin,
                            'keterangan' => $request->bom_mesin_keterangan[$i] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('produk.index')
                           ->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $produk = Produk::with(['permintaanProduksi', 'bomBahan.bahanBaku', 'bomBahan.produkWip', 'bomMesin'])->findOrFail($id);
        return view('master.produk.show', compact('produk'));
    }

    public function edit($id)
    {
        $produk = Produk::with(['bomBahan.bahanBaku', 'bomBahan.produkWip', 'bomMesin'])->findOrFail($id);
        $bahanBaku = BahanBaku::where('status', 'aktif')->orderBy('nama_bahan')->get();
        $wipProduk = Produk::where('status', 'aktif')
            ->whereIn('tipe_produk', ['kulit', 'isi'])
            ->where('id_produk', '!=', $id)
            ->orderBy('nama_produk')
            ->get();
        
        $bomOptions = collect();
        foreach ($bahanBaku as $b) {
            $bomOptions->push((object)[
                'id' => 'bahan_' . $b->id_bahan,
                'nama' => $b->nama_bahan . ' (Bahan Baku)',
                'kode' => $b->kode_bahan,
                'satuan' => $b->satuan,
            ]);
        }
        foreach ($wipProduk as $w) {
            $bomOptions->push((object)[
                'id' => 'wip_' . $w->id_produk,
                'nama' => $w->nama_produk . ' (WIP - ' . ucfirst($w->tipe_produk) . ')',
                'kode' => $w->kode_produk,
                'satuan' => $w->satuan_produk,
            ]);
        }
        
        $bahanBakuJson = $bomOptions->map(function($opt) {
            return [
                'id' => $opt->id,
                'nama' => $opt->nama,
                'kode' => $opt->kode,
                'satuan' => $opt->satuan,
            ];
        })->toJson();
        
        return view('master.produk.edit', compact('produk', 'bomOptions', 'bahanBakuJson'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'tipe_produk' => 'required|in:kulit,isi,jadi',
            'satuan_produk' => 'required|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'nullable|string',
            'bom_bahan_id.*' => 'nullable|string',
            'bom_mesin_nama.*' => 'nullable|string|max:100',
        ], [
            'nama_produk.required' => 'Nama produk harus diisi',
            'tipe_produk.required' => 'Tipe produk harus dipilih',
            'satuan_produk.required' => 'Satuan produk harus dipilih',
        ]);

        DB::beginTransaction();
        try {
            $produk->update([
                'nama_produk' => $request->nama_produk,
                'tipe_produk' => $request->tipe_produk,
                'satuan_produk' => $request->satuan_produk,
                'status' => $request->status,
                'deskripsi' => $request->deskripsi,
            ]);

            // Hapus BOM lama dan insert ulang
            $produk->bomBahan()->delete();
            $produk->bomMesin()->delete();

            // Simpan BOM Bahan baru
            if ($request->bom_bahan_id) {
                foreach ($request->bom_bahan_id as $i => $prefixedId) {
                    if ($prefixedId) {
                        $parts = explode('_', $prefixedId);
                        if (count($parts) === 2) {
                            $type = $parts[0];
                            $idVal = $parts[1];
                            
                            $bomData = [
                                'id_produk' => $produk->id_produk,
                                'jumlah_kebutuhan' => 0.00,
                                'keterangan' => $request->bom_bahan_keterangan[$i] ?? null,
                            ];
                            
                            if ($type === 'bahan') {
                                $bomData['id_bahan'] = $idVal;
                                $bomData['id_produk_wip'] = null;
                            } elseif ($type === 'wip') {
                                $bomData['id_bahan'] = null;
                                $bomData['id_produk_wip'] = $idVal;
                            }
                            
                            BomBahan::create($bomData);
                        }
                    }
                }
            }

            // Simpan BOM Mesin baru
            if ($request->bom_mesin_nama) {
                foreach ($request->bom_mesin_nama as $i => $namaMesin) {
                    if ($namaMesin) {
                        BomMesin::create([
                            'id_produk' => $produk->id_produk,
                            'nama_mesin' => $namaMesin,
                            'keterangan' => $request->bom_mesin_keterangan[$i] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('produk.index')
                           ->with('success', 'Produk berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Cek apakah ada transaksi terkait
        if ($produk->permintaanProduksi()->count() > 0) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah ada job order terkait!');
        }

        $produk->delete();

        return redirect()->route('produk.index')
                       ->with('success', 'Produk berhasil dihapus!');
    }
}