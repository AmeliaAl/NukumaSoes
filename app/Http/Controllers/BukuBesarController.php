<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalUmumDetail;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'id_akun' => 'nullable|exists:akun,id_akun',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $akuns = Akun::where('status', 'aktif')->orderBy('kode_akun', 'asc')->get();
        $selectedAkun = null;
        $jurnalDetails = [];
        $saldoAwal = 0;

        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'));

        if ($request->filled('id_akun')) {
            $selectedAkun = Akun::findOrFail($request->id_akun);

            $saldoAwalDebit = JurnalUmumDetail::where('id_akun', $selectedAkun->id_akun)
                ->whereHas('jurnalUmum', function ($query) use ($tanggalMulai) {
                    $query->where('tanggal', '<', $tanggalMulai);
                })
                ->sum('debit');

            $saldoAwalKredit = JurnalUmumDetail::where('id_akun', $selectedAkun->id_akun)
                ->whereHas('jurnalUmum', function ($query) use ($tanggalMulai) {
                    $query->where('tanggal', '<', $tanggalMulai);
                })
                ->sum('kredit');

            $saldoAwal = $selectedAkun->saldo_normal === 'debit'
                ? $saldoAwalDebit - $saldoAwalKredit
                : $saldoAwalKredit - $saldoAwalDebit;

            $jurnalDetails = JurnalUmumDetail::with(['jurnalUmum'])
                ->where('id_akun', $selectedAkun->id_akun)
                ->whereHas('jurnalUmum', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                    $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
                })
                ->get()
                ->sortBy(function ($detail) {
                    return $detail->jurnalUmum->tanggal->format('Y-m-d') . '-' . $detail->id_jurnal;
                });
        }

        return view('laporan.buku-besar.index', compact(
            'akuns',
            'selectedAkun',
            'jurnalDetails',
            'saldoAwal',
            'tanggalMulai',
            'tanggalAkhir'
        ));
    }
}
