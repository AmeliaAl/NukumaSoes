<?php

namespace App\Exports;

use App\Models\JurnalUmum;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JurnalUmumExport implements FromView, ShouldAutoSize
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

        $entries = JurnalUmum::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
            
        return view('laporan.jurnal-umum-excel', [
            'entries' => $entries,
            'periode' => $this->periode
        ]);
    }
}
