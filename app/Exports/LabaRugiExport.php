<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LabaRugiExport implements FromView, ShouldAutoSize
{
    protected $periode;

    public function __construct($periode)
    {
        $this->periode = $periode;
    }

    public function view(): View
    {
        $parts = explode('-', $this->periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        $akunJurnal = \App\Models\JurnalUmum::select('keterangan', 'ref')
            ->distinct()
            ->orderBy('ref', 'asc')
            ->get();

        $pendapatan = [];
        $beban = [];
        $totalPendapatan = 0;
        $totalBeban = 0;

        foreach ($akunJurnal as $akun) {
            $sumDebit = \App\Models\JurnalUmum::where('keterangan', $akun->keterangan)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('debit');
                
            $sumKredit = \App\Models\JurnalUmum::where('keterangan', $akun->keterangan)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('kredit');

            $prefix = substr($akun->ref, 0, 1);

            if (in_array($prefix, ['4', '8'])) {
                $net = $sumKredit - $sumDebit;
                if ($net != 0) {
                    $pendapatan[] = [
                        'keterangan' => $akun->keterangan,
                        'jumlah' => $net,
                    ];
                    $totalPendapatan += $net;
                }
            } elseif (in_array($prefix, ['5', '6', '7', '9'])) {
                $net = $sumDebit - $sumKredit;
                if ($net != 0) {
                    $beban[] = [
                        'keterangan' => $akun->keterangan,
                        'jumlah' => $net,
                    ];
                    $totalBeban += $net;
                }
            }
        }

        $labaRugi = $totalPendapatan - $totalBeban;

        return view('laporan.laba-rugi-excel', [
            'pendapatan' => $pendapatan,
            'beban' => $beban,
            'totalPendapatan' => $totalPendapatan,
            'totalBeban' => $totalBeban,
            'labaRugi' => $labaRugi,
            'periode' => $this->periode,
        ]);
    }
}
