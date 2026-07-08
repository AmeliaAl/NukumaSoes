<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanBahanBaku;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermintaanBahanBakuController extends Controller
{
    public function index()
    {
        $permintaanBahan = PermintaanBahanBaku::with(['details.bahanBaku', 'admin', 'penerimaanBahanBaku'])
                                        ->orderBy('tanggal_permintaan', 'desc')
                                        ->get();

        return view('transaksi.permintaan-bahan-baku.index', compact('permintaanBahan'));
    }

    public function create()
    {
        $lastPermintaan = PermintaanBahanBaku::orderBy('nomor_permintaan', 'desc')->first();
        
        if ($lastPermintaan) {
            $lastNumber = (int) substr($lastPermintaan->nomor_permintaan, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $nomor_permintaan = 'PBB-' . date('Ymd') . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $bahanBaku = BahanBaku::where('status', 'aktif')->orderBy('nama_bahan')->get();
        $bahanBakuJson = $bahanBaku->map(function($b) {
            return [
                'id' => $b->id_bahan,
                'nama' => $b->nama_bahan,
                'kode' => $b->kode_bahan,
                'satuan' => $b->satuan,
                'satuan_beli' => $b->satuan_beli,
                'isi_per_kemasan' => floatval($b->isi_per_kemasan ?? 1)
            ];
        })->toJson();

        return view('transaksi.permintaan-bahan-baku.create', compact('nomor_permintaan', 'bahanBaku', 'bahanBakuJson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_permintaan' => 'required|string|max:50|unique:permintaan_bahan_baku,nomor_permintaan',
            'tanggal_permintaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'bahan' => 'required|array|min:1',
            'bahan.*.id_bahan' => 'required|distinct|exists:bahan_baku,id_bahan',
            'bahan.*.jumlah' => 'required|numeric|min:0.01',
        ], [
            'nomor_permintaan.unique' => 'Nomor permintaan sudah digunakan',
            'bahan.required' => 'Minimal satu bahan baku harus ditambahkan',
            'bahan.*.id_bahan.required' => 'Bahan Baku harus dipilih',
            'bahan.*.jumlah.required' => 'Jumlah Permintaan harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $permintaan = PermintaanBahanBaku::create([
                'nomor_permintaan' => $request->nomor_permintaan,
                'id_admin' => Auth::guard('admin')->id(),
                'tanggal_permintaan' => $request->tanggal_permintaan,
                'status_permintaan' => 'aktif',
                'keterangan' => $request->keterangan,
            ]);

            foreach ($request->bahan as $b) {
                $bahan = BahanBaku::findOrFail($b['id_bahan']);
                $isiPerKemasan = $bahan->isi_per_kemasan > 0 ? floatval($bahan->isi_per_kemasan) : 1;
                $jumlahPakai = $b['jumlah'] * $isiPerKemasan;
                
                $permintaan->details()->create([
                    'id_bahan' => $b['id_bahan'],
                    'jumlah_permintaan' => $jumlahPakai,
                    'jumlah_diterima' => 0,
                    'status_penerimaan' => 'belum',
                ]);
            }
            DB::commit();

            return redirect()->route('permintaan-bahan-baku.index')
                           ->with('success', 'Permintaan bahan baku berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $permintaan = PermintaanBahanBaku::with(['details.bahanBaku', 'admin', 'penerimaanBahanBaku.details'])
                                        ->findOrFail($id);

        return view('transaksi.permintaan-bahan-baku.show', compact('permintaan'));
    }

    public function edit($id)
    {
        $permintaan = PermintaanBahanBaku::with('details')->findOrFail($id);

        if ($permintaan->status_penerimaan === 'completed') {
            return redirect()->route('permintaan-bahan-baku.index')
                           ->with('error', 'Permintaan yang sudah selesai diterima tidak bisa diedit!');
        }

        $bahanBaku = BahanBaku::where('status', 'aktif')->orderBy('nama_bahan')->get();
        $bahanBakuJson = $bahanBaku->map(function($b) {
            return [
                'id' => $b->id_bahan,
                'nama' => $b->nama_bahan,
                'kode' => $b->kode_bahan,
                'satuan' => $b->satuan,
                'satuan_beli' => $b->satuan_beli,
                'isi_per_kemasan' => floatval($b->isi_per_kemasan ?? 1)
            ];
        })->toJson();

        return view('transaksi.permintaan-bahan-baku.edit', compact('permintaan', 'bahanBaku', 'bahanBakuJson'));
    }

    public function update(Request $request, $id)
    {
        $permintaan = PermintaanBahanBaku::findOrFail($id);

        if ($permintaan->status_penerimaan === 'completed') {
            return back()->with('error', 'Permintaan yang sudah selesai diterima tidak bisa diedit!');
        }

        $request->validate([
            'tanggal_permintaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'bahan' => 'required|array|min:1',
            'bahan.*.id_bahan' => 'required|distinct|exists:bahan_baku,id_bahan',
            'bahan.*.jumlah' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $permintaan->update([
                'tanggal_permintaan' => $request->tanggal_permintaan,
                'keterangan' => $request->keterangan,
            ]);

            // Hapus detail lama dan ganti dengan yang baru
            $permintaan->details()->delete();

            foreach ($request->bahan as $b) {
                $bahan = BahanBaku::findOrFail($b['id_bahan']);
                $isiPerKemasan = $bahan->isi_per_kemasan > 0 ? floatval($bahan->isi_per_kemasan) : 1;
                $jumlahPakai = $b['jumlah'] * $isiPerKemasan;
                
                $permintaan->details()->create([
                    'id_bahan' => $b['id_bahan'],
                    'jumlah_permintaan' => $jumlahPakai,
                    'jumlah_diterima' => 0,
                    'status_penerimaan' => 'belum',
                ]);
            }
            DB::commit();

            return redirect()->route('permintaan-bahan-baku.index')
                           ->with('success', 'Permintaan bahan baku berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $permintaan = PermintaanBahanBaku::findOrFail($id);

        if ($permintaan->status_penerimaan === 'completed') {
            return back()->with('error', 'Permintaan yang sudah selesai diterima tidak bisa dihapus!');
        }

        $permintaan->delete();

        return redirect()->route('permintaan-bahan-baku.index')
                       ->with('success', 'Permintaan bahan baku berhasil dihapus!');
    }

}
