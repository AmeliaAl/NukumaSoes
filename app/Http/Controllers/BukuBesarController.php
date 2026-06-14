<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalUmumDetail;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $akuns = Akun::where('status', 'aktif')->orderBy('kode_akun', 'asc')->get();
        $selectedAkun = null;
        $jurnalDetails = [];
        $saldoAwal = 0;
        $saldoAkhir = 0;
        
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'));

        if ($request->filled('id_akun')) {
            $selectedAkun = Akun::findOrFail($request->id_akun);
            
            // Hitung saldo awal (Sebelum tanggal mulai)
            $saldoAwalDebit = JurnalUmumDetail::where('id_akun', $selectedAkun->id_akun)
                                              ->whereHas('jurnalUmum', function($q) use ($tanggalMulai) {
                                                  $q->where('tanggal', '<', $tanggalMulai);
                                              })->sum('debit');
                                              
            $saldoAwalKredit = JurnalUmumDetail::where('id_akun', $selectedAkun->id_akun)
                                              ->whereHas('jurnalUmum', function($q) use ($tanggalMulai) {
                                                  $q->where('tanggal', '<', $tanggalMulai);
                                              })->sum('kredit');
                                              
            if ($selectedAkun->saldo_normal === 'debit') {
                $saldoAwal = $saldoAwalDebit - $saldoAwalKredit;
            } else {
                $saldoAwal = $saldoAwalKredit - $saldoAwalDebit;
            }

            // Ambil mutasi pada periode
            $jurnalDetails = JurnalUmumDetail::with(['jurnalUmum'])
                                             ->where('id_akun', $selectedAkun->id_akun)
                                             ->whereHas('jurnalUmum', function($q) use ($tanggalMulai, $tanggalAkhir) {
                                                 $q->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
                                             })
                                             ->get()
                                             ->sortBy(function($detail) {
                                                 return $detail->jurnalUmum->tanggal->format('Y-m-d') . '-' . $detail->id_jurnal;
                                             });
        }

        return view('laporan.buku-besar.index', compact(
            'akuns', 'selectedAkun', 'jurnalDetails', 'saldoAwal', 'tanggalMulai', 'tanggalAkhir'
        ));
    }
}
