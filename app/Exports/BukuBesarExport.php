<?php

namespace App\Exports;

use App\Models\JurnalUmum;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BukuBesarExport implements FromView, ShouldAutoSize
{
    protected $periode;
    protected $namaAkun;

    public function __construct($periode, $namaAkun)
    {
        $this->periode = $periode;
        $this->namaAkun = $namaAkun;
    }

    public function view(): View
    {
        $parts = explode('-', $this->periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        $entries = [];
        
        if ($this->namaAkun) {
            $firstDayOfMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $coa = \App\Models\Coa::where('nama_akun', $this->namaAkun)->first();
            $isCreditNormal = false;
            if ($coa) {
                $prefix = substr($coa->kode_akun, 0, 1);
                if (in_array($prefix, ['2', '3', '4'])) {
                    $isCreditNormal = true;
                }
            }

            $saldo = 0;
            
            // Get previous months' balance for Saldo Awal
            $prevDebit = JurnalUmum::where('keterangan', $this->namaAkun)
                ->where('tanggal', '<', $firstDayOfMonth->format('Y-m-d'))
                ->sum('debit');
            $prevKredit = JurnalUmum::where('keterangan', $this->namaAkun)
                ->where('tanggal', '<', $firstDayOfMonth->format('Y-m-d'))
                ->sum('kredit');
            
            if ($isCreditNormal) {
                $saldo = $prevKredit - $prevDebit;
            } else {
                $saldo = $prevDebit - $prevKredit;
            }

            $entries[] = [
                'tanggal' => $firstDayOfMonth->format('Y-m-d'),
                'keterangan' => 'Saldo Awal',
                'debit' => 0,
                'kredit' => 0,
                'saldo' => $saldo,
                'is_saldo_awal' => true,
                'is_saldo_akhir' => false,
            ];

            $jurnalEntries = JurnalUmum::where('keterangan', $this->namaAkun)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            
            foreach ($jurnalEntries as $entry) {
                $lawan = JurnalUmum::where('created_at', $entry->created_at)
                    ->where('id', '!=', $entry->id)
                    ->first();
                $deskripsi = $lawan ? $lawan->keterangan : $entry->keterangan;

                $debit = $entry->debit;
                $kredit = $entry->kredit;
                
                if ($isCreditNormal) {
                    $saldo = $saldo + $kredit - $debit;
                } else {
                    $saldo = $saldo + $debit - $kredit;
                }
                
                $entries[] = [
                    'tanggal' => $entry->tanggal,
                    'keterangan' => $deskripsi,
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'saldo' => $saldo,
                    'is_saldo_awal' => false,
                    'is_saldo_akhir' => false,
                ];
            }

            $lastDayOfMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
            $entries[] = [
                'tanggal' => $lastDayOfMonth->format('Y-m-d'),
                'keterangan' => 'SALDO AKHIR',
                'debit' => 0,
                'kredit' => 0,
                'saldo' => $saldo,
                'is_saldo_awal' => false,
                'is_saldo_akhir' => true,
            ];
        }
            
        return view('laporan.buku-besar-excel', [
            'entries' => $entries,
            'periode' => $this->periode,
            'namaAkun' => $this->namaAkun
        ]);
    }
}
