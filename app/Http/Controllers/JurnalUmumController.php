<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use Illuminate\Http\Request;

class JurnalUmumController extends Controller
{
    public function index(Request $request)
    {
        $query = JurnalUmum::with(['detail.akun', 'admin'])->orderBy('tanggal', 'desc')->orderBy('id_jurnal', 'desc');

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
        } elseif ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', $request->tanggal_mulai);
        }

        $jurnals = $query->paginate(20)->withQueryString();

        return view('laporan.jurnal-umum.index', compact('jurnals'));
    }

    public function show($id)
    {
        $jurnal = JurnalUmum::with(['detail.akun', 'admin'])->findOrFail($id);
        
        // Cek referensi (Opsional, untuk link ke transaksi asal)
        $routeReferensi = '#';
        if ($jurnal->tipe_referensi == 'penerimaan_bahan_baku') {
            $routeReferensi = route('penerimaan-bahan-baku.show', $jurnal->id_referensi);
        } elseif ($jurnal->tipe_referensi == 'permintaan_produksi') {
            $routeReferensi = route('permintaan-produksi.show', $jurnal->id_referensi);
        } elseif ($jurnal->tipe_referensi == 'pemakaian_bahan_baku') {
            $routeReferensi = route('pemakaian-bahan-baku.index');
        } elseif ($jurnal->tipe_referensi == 'biaya_overhead_pabrik') {
            $routeReferensi = route('biaya-overhead-pabrik.index');
        } elseif ($jurnal->tipe_referensi == 'pengeluaran_bop_aktual') {
            $routeReferensi = route('pengeluaran-bop.index');
        } elseif ($jurnal->tipe_referensi == 'biaya_tenaga_kerja') {
            $routeReferensi = route('biaya-tenaga-kerja.index');
        } elseif ($jurnal->tipe_referensi == 'insentif_mingguan') {
            $routeReferensi = route('kehadiran.rekap-mingguan');
        }
        
        return view('laporan.jurnal-umum.show', compact('jurnal', 'routeReferensi'));
    }
}
