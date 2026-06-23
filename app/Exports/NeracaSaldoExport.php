<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class NeracaSaldoExport implements FromView, ShouldAutoSize
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
        $lastDayOfMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $akunJurnal = \App\Models\JurnalUmum::select('keterangan', 'ref')
            ->distinct()
            ->orderBy('ref', 'asc')
            ->get();

        $entries = [];
        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($akunJurnal as $akun) {
            $sumDebit = \App\Models\JurnalUmum::where('keterangan', $akun->keterangan)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('debit');
                
            $sumKredit = \App\Models\JurnalUmum::where('keterangan', $akun->keterangan)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('kredit');
                
            $net = $sumDebit - $sumKredit;
            $debit = 0;
            $kredit = 0;
            
            if ($net > 0) {
                $debit = $net;
            } elseif ($net < 0) {
                $kredit = abs($net);
            }
            
            if ($debit > 0 || $kredit > 0) {
                $entries[] = [
                    'ref' => $akun->ref,
                    'keterangan' => $akun->keterangan,
                    'debit' => $debit,
                    'kredit' => $kredit,
                ];
                $totalDebit += $debit;
                $totalKredit += $kredit;
            }
        }

        return view('laporan.neraca-saldo-excel', [
            'entries' => $entries,
            'periode' => $this->periode,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
        ]);
    }
}
