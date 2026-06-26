<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenerimaanBahanBaku;
use App\Models\PermintaanBahanBaku;
use App\Models\BahanBaku;
use App\Models\StokBahanBaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenerimaanBahanBakuController extends Controller
{
    public function cariPermintaan(Request $request)
    {
        $nomor = $request->query('nomor');
        
        if (!$nomor) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor permintaan tidak boleh kosong'
            ]);
        }
        
        $permintaan = PermintaanBahanBaku::with('details.bahanBaku')
                                        ->where('nomor_permintaan', $nomor)
                                        ->where('status_permintaan', 'aktif')
                                        ->first();
        
        if (!$permintaan) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak ditemukan'
            ]);
        }
        
        // Cek apakah sudah completed
        if ($permintaan->status_penerimaan === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan sudah complete (semua sudah diterima)'
            ]);
        }

        $details = $permintaan->details->map(function($d) {
            $sisaBelumDiterima = $d->jumlah_permintaan - $d->jumlah_diterima;
            return [
                'id_permintaan_detail' => $d->id_permintaan_detail,
                'id_bahan' => $d->id_bahan,
                'nama_bahan' => $d->bahanBaku->nama_bahan,
                'kode_bahan' => $d->bahanBaku->kode_bahan,
                'jumlah_permintaan' => $d->jumlah_permintaan,
                'jumlah_diterima' => $d->jumlah_diterima,
                'sisa_belum_diterima' => $sisaBelumDiterima,
                'satuan' => $d->bahanBaku->satuan,
                'satuan_beli' => $d->bahanBaku->satuan_beli,
                'isi_per_kemasan' => floatval($d->bahanBaku->isi_per_kemasan ?? 1),
            ];
        });
        
        return response()->json([
            'success' => true,
            'permintaan' => [
                'id_permintaan_bahan' => $permintaan->id_permintaan_bahan,
                'nomor_permintaan' => $permintaan->nomor_permintaan,
                'tanggal_permintaan' => $permintaan->tanggal_permintaan->format('d/m/Y'),
            ],
            'details' => $details
        ]);
    }

    public function index()
    {
        $penerimaanBahan = PenerimaanBahanBaku::with(['permintaanBahanBaku', 'details.bahanBaku', 'admin'])
                                        ->orderBy('tanggal_penerimaan', 'desc')
                                        ->get();

        return view('transaksi.penerimaan-bahan-baku.index', compact('penerimaanBahan'));
    }

    public function create()
    {
        $lastPenerimaan = PenerimaanBahanBaku::orderBy('nomor_penerimaan', 'desc')->first();
        
        if ($lastPenerimaan) {
            $lastNumber = (int) substr($lastPenerimaan->nomor_penerimaan, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $nomor_penerimaan = 'TRM-' . date('Ymd') . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

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
        
        $permintaanData = null;
        $nomorPermintaanAuto = null;
        
        if (request('permintaan')) {
            $nomorPermintaanAuto = request('permintaan');
            
            $permintaan = PermintaanBahanBaku::with('details.bahanBaku')
                                            ->where('nomor_permintaan', $nomorPermintaanAuto)
                                            ->where('status_permintaan', 'aktif')
                                            ->first();
            
            if ($permintaan) {
                $details = $permintaan->details->map(function($d) {
                    return [
                        'id_permintaan_detail' => $d->id_permintaan_detail,
                        'id_bahan' => $d->id_bahan,
                        'nama_bahan' => $d->bahanBaku->nama_bahan,
                        'kode_bahan' => $d->bahanBaku->kode_bahan,
                        'jumlah_permintaan' => $d->jumlah_permintaan,
                        'jumlah_diterima' => $d->jumlah_diterima,
                        'sisa_belum_diterima' => $d->jumlah_permintaan - $d->jumlah_diterima,
                        'satuan' => $d->bahanBaku->satuan,
                        'satuan_beli' => $d->bahanBaku->satuan_beli,
                        'isi_per_kemasan' => floatval($d->bahanBaku->isi_per_kemasan ?? 1),
                    ];
                });

                $permintaanData = [
                    'id_permintaan_bahan' => $permintaan->id_permintaan_bahan,
                    'nomor_permintaan' => $permintaan->nomor_permintaan,
                    'tanggal_permintaan' => $permintaan->tanggal_permintaan->format('d/m/Y'),
                    'details' => $details
                ];
            }
        }

        return view('transaksi.penerimaan-bahan-baku.create', compact(
            'nomor_penerimaan', 
            'bahanBaku', 
            'bahanBakuJson',
            'permintaanData',
            'nomorPermintaanAuto'
        ));
    }

    public function store(Request $request)
    {
        Log::info('Penerimaan Bahan Request Data:', $request->all());

        try {
            $request->validate([
                'nomor_penerimaan' => 'required|string|max:50|unique:penerimaan_bahan_baku,nomor_penerimaan',
                'tanggal_penerimaan' => 'required|date',
                'id_permintaan_bahan' => 'nullable|exists:permintaan_bahan_baku,id_permintaan_bahan',
                'supplier' => 'nullable|string|max:100',
                'keterangan' => 'nullable|string',
                'bahan' => 'required|array|min:1',
                'bahan.*.id_bahan' => 'required|distinct|exists:bahan_baku,id_bahan',
                'bahan.*.jumlah_diterima' => 'required|numeric|min:0.01',
                'bahan.*.harga_per_satuan' => 'required|numeric|min:0',
                'bahan.*.id_permintaan_detail' => 'nullable|exists:permintaan_bahan_baku_detail,id_permintaan_detail'
            ]);

            // VALIDASI TAMBAHAN: Jika dari permintaan, cek sisa
            if ($request->id_permintaan_bahan) {
                foreach ($request->bahan as $b) {
                    if (isset($b['id_permintaan_detail'])) {
                        $detailPermintaan = \App\Models\PermintaanBahanBakuDetail::find($b['id_permintaan_detail']);
                        if ($detailPermintaan) {
                            if ((int) $detailPermintaan->id_permintaan_bahan !== (int) $request->id_permintaan_bahan
                                || (int) $detailPermintaan->id_bahan !== (int) $b['id_bahan']) {
                                return back()->withErrors([
                                    'bahan' => 'Detail bahan tidak sesuai dengan permintaan bahan baku yang dipilih.'
                                ])->withInput();
                            }

                            $bahanData = BahanBaku::find($b['id_bahan']);
                            $isiPerKemasan = $bahanData->isi_per_kemasan > 0 ? floatval($bahanData->isi_per_kemasan) : 1;
                            
                            $jumlahDiterimaPakai = $b['jumlah_diterima'] * $isiPerKemasan;
                            $sisaBelumDiterima = $detailPermintaan->jumlah_permintaan - $detailPermintaan->jumlah_diterima;
                            
                            // Toleransi float point error
                            if ($jumlahDiterimaPakai > ($sisaBelumDiterima + 0.01)) {
                                return back()->withErrors([
                                    'bahan' => "Jumlah (". $jumlahDiterimaPakai ." {$bahanData->satuan}) melebihi sisa permintaan untuk bahan tertentu! Sisa maksimal: $sisaBelumDiterima {$bahanData->satuan}"
                                ])->withInput();
                            }
                        }
                    }
                }
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi gagal:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            $penerimaan = PenerimaanBahanBaku::create([
                'nomor_penerimaan' => $request->nomor_penerimaan,
                'id_permintaan_bahan' => $request->id_permintaan_bahan,
                'id_admin' => Auth::guard('admin')->id(),
                'tanggal_penerimaan' => $request->tanggal_penerimaan,
                'supplier' => $request->supplier,
                'keterangan' => $request->keterangan,
            ]);

            $totalSemuaBiaya = 0;

            foreach ($request->bahan as $b) {
                $bahan = BahanBaku::findOrFail($b['id_bahan']);
                $isiPerKemasan = $bahan->isi_per_kemasan > 0 ? floatval($bahan->isi_per_kemasan) : 1;
                
                // Konversi dari satuan beli ke satuan pakai
                $jumlahPakai = $b['jumlah_diterima'] * $isiPerKemasan;
                $hargaPakai = $b['harga_per_satuan'] / $isiPerKemasan;
                
                $totalBiaya = $jumlahPakai * $hargaPakai; // = $b['jumlah_diterima'] * $b['harga_per_satuan']
                $totalSemuaBiaya += $totalBiaya;

                // 1. Simpan detail penerimaan (dalam satuan pakai)
                $detail = $penerimaan->details()->create([
                    'id_bahan' => $b['id_bahan'],
                    'jumlah_diterima' => $jumlahPakai,
                    'harga_per_satuan' => $hargaPakai,
                    'total_biaya' => $totalBiaya,
                ]);

                // 2. UPDATE TRACKING PERMINTAAN DETAIL
                if ($request->id_permintaan_bahan && isset($b['id_permintaan_detail'])) {
                    $detailPermintaan = \App\Models\PermintaanBahanBakuDetail::find($b['id_permintaan_detail']);
                    if ($detailPermintaan) {
                        $detailPermintaan->jumlah_diterima += $jumlahPakai;
                        
                        // Update status detail
                        if ($detailPermintaan->jumlah_diterima >= $detailPermintaan->jumlah_permintaan - 0.01) {
                            $detailPermintaan->status_penerimaan = 'completed';
                        } elseif ($detailPermintaan->jumlah_diterima > 0) {
                            $detailPermintaan->status_penerimaan = 'partial';
                        }
                        $detailPermintaan->save();
                    }
                }

                // 3. Tambahkan ke stok_bahan_baku
                StokBahanBaku::create([
                    'id_bahan' => $b['id_bahan'],
                    'id_penerimaan' => $penerimaan->id_penerimaan,
                    'tanggal_masuk' => $request->tanggal_penerimaan,
                    'jumlah_masuk' => $jumlahPakai,
                    'harga_per_satuan' => $hargaPakai,
                    'sisa_stok' => $jumlahPakai,
                    'status' => 'tersedia',
                ]);

                // 4. Update stok_saat_ini
                $stokLama = $bahan->stok_saat_ini;
                $stokBaru = $stokLama + $jumlahPakai;
                
                $bahan->stok_saat_ini = $stokBaru;
                $bahan->save();
            }

            // Jurnal pembelian/penerimaan persediaan menjadi tanggung jawab modul
            // pembelian dan gudang. Modul produksi hanya menerima kuantitas stok.

            DB::commit();

            return redirect()->route('penerimaan-bahan-baku.index')
                           ->with('success', 'Penerimaan bahan baku berhasil dicatat! Total biaya: Rp ' . number_format($totalSemuaBiaya, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saat menyimpan penerimaan:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $penerimaan = PenerimaanBahanBaku::with([
                                            'permintaanBahanBaku', 
                                            'details.bahanBaku', 
                                            'admin'
                                        ])
                                        ->findOrFail($id);

        return view('transaksi.penerimaan-bahan-baku.show', compact('penerimaan'));
    }

    public function edit($id)
    {
        // Disable edit untuk multiple item krn kompleksitas stok dan akuntansi
        return redirect()->route('penerimaan-bahan-baku.index')
                       ->with('error', 'Edit penerimaan bahan baku tidak didukung. Harap hapus dan buat ulang jika ada kesalahan.');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('penerimaan-bahan-baku.index')
                       ->with('error', 'Edit penerimaan bahan baku tidak didukung.');
    }

    public function destroy($id)
    {
        $penerimaan = PenerimaanBahanBaku::with('details')->findOrFail($id);

        // Cek apakah stok sudah dipakai
        $stokTerpakai = StokBahanBaku::where('id_penerimaan', $id)
                            ->whereColumn('sisa_stok', '<', 'jumlah_masuk')
                            ->exists();
                            
        if ($stokTerpakai) {
            return back()->with('error', 'Penerimaan tidak dapat dihapus karena sebagian stok sudah dipakai dalam produksi!');
        }

        DB::beginTransaction();
        try {
            foreach ($penerimaan->details as $detail) {
                // UPDATE TRACKING PERMINTAAN
                if ($penerimaan->id_permintaan_bahan) {
                    // Cari detail permintaan yg sesuai
                    $permintaanDetail = \App\Models\PermintaanBahanBakuDetail::where('id_permintaan_bahan', $penerimaan->id_permintaan_bahan)
                                            ->where('id_bahan', $detail->id_bahan)
                                            ->first();
                                            
                    if ($permintaanDetail) {
                        $permintaanDetail->jumlah_diterima -= $detail->jumlah_diterima;
                        
                        // Update status detail
                        if ($permintaanDetail->jumlah_diterima <= 0) {
                            $permintaanDetail->status_penerimaan = 'belum';
                        } else {
                            $permintaanDetail->status_penerimaan = 'partial';
                        }
                        $permintaanDetail->save();
                    }
                }

                // Kurangi stok di bahan_baku
                $bahan = $detail->bahanBaku;
                $bahan->stok_saat_ini -= $detail->jumlah_diterima;
                
                // Recalculate harga_rata_rata if possible, or just leave it.
                // Usually we just keep the average price, but adjust stock.
                $bahan->save();
            }

            // Hapus dari stok_bahan_baku
            StokBahanBaku::where('id_penerimaan', $id)->delete();

            // Hapus detail
            $penerimaan->details()->delete();

            // JURNAL BALIK: (TBD: if you have reverse journal logic)
            // Currently deleting penerimaan does not automatically delete journal, 
            // you might want to delete the related journal.
            $jurnal = \App\Models\JurnalUmum::where('tipe_referensi', 'penerimaan_bahan_baku')
                            ->where('id_referensi', $id)
                            ->first();
            if ($jurnal) {
                \App\Models\JurnalUmumDetail::where('id_jurnal', $jurnal->id_jurnal)->delete();
                $jurnal->delete();
            }

            // Hapus penerimaan
            $penerimaan->delete();

            DB::commit();

            return redirect()->route('penerimaan-bahan-baku.index')
                           ->with('success', 'Penerimaan bahan baku berhasil dihapus, stok dan jurnal dikembalikan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
