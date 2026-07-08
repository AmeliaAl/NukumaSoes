<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use Illuminate\Http\Request;

class JurnalUmumController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $query = JurnalUmum::with(['detail.akun', 'admin'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_jurnal', 'desc');

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
        $routeReferensi = $this->routeReferensi($jurnal);

        return view('laporan.jurnal-umum.show', compact('jurnal', 'routeReferensi'));
    }

    private function routeReferensi(JurnalUmum $jurnal): string
    {
        if ($jurnal->tipe_referensi === 'penerimaan_bahan_baku') {
            return route('penerimaan-bahan-baku.show', $jurnal->id_referensi);
        }

        if ($jurnal->tipe_referensi === 'permintaan_produksi') {
            return route('permintaan-produksi.show', $jurnal->id_referensi);
        }

        if ($jurnal->tipe_referensi === 'pemakaian_bahan_baku') {
            return route('pemakaian-bahan-baku.index');
        }

        if ($jurnal->tipe_referensi === 'biaya_overhead_pabrik') {
            return route('biaya-overhead-pabrik.index');
        }

        if ($jurnal->tipe_referensi === 'pengeluaran_bop_aktual') {
            return route('pengeluaran-bop.index');
        }

        if ($jurnal->tipe_referensi === 'biaya_tenaga_kerja') {
            return route('biaya-tenaga-kerja.index');
        }

        if ($jurnal->tipe_referensi === 'insentif_mingguan') {
            return route('kehadiran.rekap-mingguan');
        }

        return '#';
    }
}
