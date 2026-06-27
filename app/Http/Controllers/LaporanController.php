<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\JurnalUmumExport;
use App\Exports\BukuBesarExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function keuangan()
    {
        return view('laporan.keuangan');
    }

    public function jurnalUmum(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $parts = explode('-', $periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        $entries = \App\Models\JurnalUmum::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where(function ($q) {
                $q->where('debit', '>', 0)->orWhere('kredit', '>', 0);
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
        
        $coaAccounts = \App\Models\Coa::orderBy('kode_akun', 'asc')->get();
        return view('laporan.jurnal-umum', compact('entries', 'coaAccounts', 'periode'));
    }

    public function exportExcelJurnalUmum(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        return Excel::download(new JurnalUmumExport($periode), 'jurnal_umum_'.date('YmdHis').'.xlsx');
    }

    public function exportPdfJurnalUmum(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $parts = explode('-', $periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        $entries = \App\Models\JurnalUmum::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where(function ($q) {
                $q->where('debit', '>', 0)->orWhere('kredit', '>', 0);
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
            
        $pdf = Pdf::loadView('laporan.jurnal-umum-pdf', compact('entries', 'periode'));
        return $pdf->download('jurnal_umum_'.date('YmdHis').'.pdf');
    }

    public function storeJurnalUmum(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|array|min:1',
            'keterangan.*' => 'required|string|max:255',
            'ref' => 'required|array',
            'debit' => 'required|array',
            'debit.*' => 'nullable|numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->keterangan as $index => $ket) {
            // Only save if either debit or kredit is > 0
            $debit = $request->debit[$index] ?? 0;
            $kredit = $request->kredit[$index] ?? 0;

            if ($debit > 0 || $kredit > 0) {
                \App\Models\JurnalUmum::create([
                    'tanggal' => $request->tanggal,
                    'keterangan' => $ket,
                    'ref' => $request->ref[$index] ?? null,
                    'debit' => $debit,
                    'kredit' => $kredit,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Entry jurnal berhasil ditambahkan.');
    }

    public function destroyJurnalUmum($id)
    {
        $entry = \App\Models\JurnalUmum::findOrFail($id);
        $entry->delete();

        return redirect()->back()->with('success', 'Entry jurnal berhasil dihapus.');
    }

    public function bukuBesar(Request $request)
    {
        $akunJurnal = \App\Models\JurnalUmum::select('keterangan')
            ->distinct()
            ->orderBy('keterangan', 'asc')
            ->get();
        $namaAkun = $request->get('nama_akun');
        $periode = $request->get('periode', date('Y-m'));
        $parts = explode('-', $periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        $entries = [];
        
        if ($namaAkun) {
            $firstDayOfMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $coa = \App\Models\Coa::where('nama_akun', $namaAkun)->first();
            $isCreditNormal = false;
            if ($coa) {
                $prefix = substr($coa->kode_akun, 0, 1);
                // 2: Liabilities, 3: Equity, 4: Revenue are usually Credit Normal
                if (in_array($prefix, ['2', '3', '4'])) {
                    $isCreditNormal = true;
                }
            }

            $saldo = 0;
            
            // Get previous months' balance for Saldo Awal
            $prevDebit = \App\Models\JurnalUmum::where('keterangan', $namaAkun)
                ->where('tanggal', '<', $firstDayOfMonth->format('Y-m-d'))
                ->sum('debit');
            $prevKredit = \App\Models\JurnalUmum::where('keterangan', $namaAkun)
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

            $jurnalEntries = \App\Models\JurnalUmum::where('keterangan', $namaAkun)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            
            foreach ($jurnalEntries as $entry) {
                // Find contrary account
                $lawan = \App\Models\JurnalUmum::where('created_at', $entry->created_at)
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

            $lastDayOfMonth = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
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

        return view('laporan.buku-besar', compact('entries', 'akunJurnal', 'periode', 'namaAkun'));
    }

    public function exportExcelBukuBesar(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $namaAkun = $request->get('nama_akun');
        return Excel::download(new BukuBesarExport($periode, $namaAkun), 'buku_besar_'.date('YmdHis').'.xlsx');
    }

    public function exportPdfBukuBesar(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $namaAkun = $request->get('nama_akun');
        $parts = explode('-', $periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        $entries = [];
        
        if ($namaAkun) {
            $firstDayOfMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $coa = \App\Models\Coa::where('nama_akun', $namaAkun)->first();
            $isCreditNormal = false;
            if ($coa) {
                $prefix = substr($coa->kode_akun, 0, 1);
                if (in_array($prefix, ['2', '3', '4'])) {
                    $isCreditNormal = true;
                }
            }

            $saldo = 0;
            
            // Get previous months' balance for Saldo Awal
            $prevDebit = \App\Models\JurnalUmum::where('keterangan', $namaAkun)
                ->where('tanggal', '<', $firstDayOfMonth->format('Y-m-d'))
                ->sum('debit');
            $prevKredit = \App\Models\JurnalUmum::where('keterangan', $namaAkun)
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

            $jurnalEntries = \App\Models\JurnalUmum::where('keterangan', $namaAkun)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            
            foreach ($jurnalEntries as $entry) {
                $lawan = \App\Models\JurnalUmum::where('created_at', $entry->created_at)
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

            $lastDayOfMonth = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
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
            
        $pdf = Pdf::loadView('laporan.buku-besar-pdf', compact('entries', 'periode', 'namaAkun'));
        return $pdf->download('buku_besar_'.date('YmdHis').'.pdf');
    }


}
