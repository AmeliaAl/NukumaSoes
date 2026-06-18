<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\BiayaOverheadPabrik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengeluaranBopController extends Controller
{
    /**
     * Menampilkan daftar pengeluaran BOP aktual.
     */
    public function index()
    {
        $jurnals = JurnalUmum::with(['detail.akun', 'admin'])
            ->where('tipe_referensi', 'pengeluaran_bop_aktual')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_jurnal', 'desc')
            ->paginate(15);

        return view('transaksi.pengeluaran-bop.index', compact('jurnals'));
    }

    /**
     * Menampilkan form input pengeluaran BOP aktual.
     */
    public function create()
    {
        // Ambil akun untuk debit (Beban BOP / Operasional aktual)
        $akunDebit = Akun::whereIn('kode_akun', ['600', '601', '612', '613', '620', '705', '712', '713'])
            ->aktif()
            ->orderBy('kode_akun')
            ->get();

        // Ambil BOP yang belum diaktualkan (opsional: user bisa pilih untuk di-link)
        $bopBelumAktual = BiayaOverheadPabrik::with(['permintaanProduksi.produk'])
            ->belumDiaktualkan()
            ->orderBy('tanggal_overhead', 'desc')
            ->get();
            
        return view('transaksi.pengeluaran-bop.create', compact('akunDebit', 'bopBelumAktual'));
    }

    /**
     * Menyimpan pengeluaran BOP aktual dan membuat jurnal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'akun_debit' => 'required|exists:akun,id_akun',
            'bop_terkait' => 'nullable|array',
            'bop_terkait.*' => 'exists:biaya_overhead_pabrik,id_overhead',
        ]);

        $akunDebit = Akun::find($request->akun_debit);
        
        // Hardcode Akun Kredit ke 212 (Hutang lainnya) karena ini batas lingkup HPP
        $akunKredit = Akun::where('kode_akun', '212')->first();
        if (!$akunKredit) {
            return back()->with('error', 'Gagal menyimpan: Akun "Hutang lainnya" (Kode: 212) tidak ditemukan.')->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. Buat Header Jurnal
            $jurnal = JurnalUmum::create([
                'tanggal' => $request->tanggal,
                'nomor_bukti' => JurnalUmum::generateNomorBukti('BOP'), // Pakai prefix BOP
                'keterangan' => $request->keterangan,
                'id_referensi' => null, // Tidak ada tabel sumber spesifik
                'tipe_referensi' => 'pengeluaran_bop_aktual',
                'id_admin' => 1, // Hardcode admin 1 (atau sesuaikan dengan auth()->id() jika ada)
            ]);

            // 2. Buat Detail Debit (BOP Sesungguhnya)
            JurnalUmumDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun' => $akunDebit->id_akun,
                'debit' => $request->nominal,
                'kredit' => 0,
            ]);

            // 3. Buat Detail Kredit (Hutang lainnya)
            JurnalUmumDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun' => $akunKredit->id_akun,
                'debit' => 0,
                'kredit' => $request->nominal,
            ]);

            // Update Saldo Akun Debit
            $akunDebit->saldo += $request->nominal; // Saldo normal beban adalah debit
            $akunDebit->save();

            // Update Saldo Akun Kredit (Hutang lainnya, normal kredit)
            $akunKredit->saldo += $request->nominal;
            $akunKredit->save();

            // 4. Link BOP terkait (opsional) — tandai sebagai "sudah diaktualkan"
            if ($request->has('bop_terkait') && is_array($request->bop_terkait)) {
                BiayaOverheadPabrik::whereIn('id_overhead', $request->bop_terkait)
                    ->whereNull('id_jurnal_aktual')
                    ->update(['id_jurnal_aktual' => $jurnal->id_jurnal]);
            }

            DB::commit();
            return redirect()->route('pengeluaran-bop.index')
                ->with('success', 'Pengeluaran BOP Aktual berhasil disimpan dan dijurnal.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Membatalkan/menghapus pengeluaran BOP aktual.
     */
    public function destroy($id)
    {
        $jurnal = JurnalUmum::where('tipe_referensi', 'pengeluaran_bop_aktual')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Kembalikan saldo akun sebelum menghapus
            foreach ($jurnal->detail as $detail) {
                $akun = Akun::find($detail->id_akun);
                if ($akun) {
                    if ($detail->debit > 0) {
                        if ($akun->saldo_normal == 'debit') {
                            $akun->saldo -= $detail->debit;
                        } else {
                            $akun->saldo += $detail->debit;
                        }
                    }
                    if ($detail->kredit > 0) {
                        if ($akun->saldo_normal == 'kredit') {
                            $akun->saldo -= $detail->kredit;
                        } else {
                            $akun->saldo += $detail->kredit;
                        }
                    }
                    $akun->save();
                }
            }

            // Unlink BOP yang terkait jurnal ini
            BiayaOverheadPabrik::where('id_jurnal_aktual', $jurnal->id_jurnal)
                ->update(['id_jurnal_aktual' => null]);
            
            // Hapus header (detail terhapus otomatis via cascade jika db diset, tapi lebih aman eksplisit)
            JurnalUmumDetail::where('id_jurnal', $jurnal->id_jurnal)->delete();
            $jurnal->delete();
            
            DB::commit();
            return redirect()->route('pengeluaran-bop.index')
                ->with('success', 'Pengeluaran BOP Aktual berhasil dibatalkan dan jurnal ditarik kembali.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
