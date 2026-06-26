<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalUmumDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NeracaLajurController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'periode' => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ]);

        $periode = $request->get('periode', date('Y-m'));
        $parts = explode('-', $periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        $tanggalAkhir = Carbon::createFromDate($tahun, $bulan, 1)
            ->endOfMonth()
            ->format('Y-m-d');

        $akuns = Akun::where('status', 'aktif')->orderBy('kode_akun', 'asc')->get();

        $totalNsDebit = 0;
        $totalNsKredit = 0;
        $totalNsdDebit = 0;
        $totalNsdKredit = 0;
        $totalLrDebit = 0;
        $totalLrKredit = 0;
        $totalNDebit = 0;
        $totalNKredit = 0;

        $neracaData = $akuns->map(function ($akun) use (
            $tanggalAkhir,
            &$totalNsDebit,
            &$totalNsKredit,
            &$totalNsdDebit,
            &$totalNsdKredit,
            &$totalLrDebit,
            &$totalLrKredit,
            &$totalNDebit,
            &$totalNKredit
        ) {
            $debitMutasi = JurnalUmumDetail::where('id_akun', $akun->id_akun)
                ->whereHas('jurnalUmum', function ($query) use ($tanggalAkhir) {
                    $query->where('tanggal', '<=', $tanggalAkhir);
                })
                ->sum('debit');

            $kreditMutasi = JurnalUmumDetail::where('id_akun', $akun->id_akun)
                ->whereHas('jurnalUmum', function ($query) use ($tanggalAkhir) {
                    $query->where('tanggal', '<=', $tanggalAkhir);
                })
                ->sum('kredit');

            $debit = 0;
            $kredit = 0;

            if ($akun->saldo_normal === 'debit') {
                $saldo = floatval($debitMutasi) - floatval($kreditMutasi);
                if ($saldo >= 0) {
                    $debit = $saldo;
                } else {
                    $kredit = abs($saldo);
                }
            } else {
                $saldo = floatval($kreditMutasi) - floatval($debitMutasi);
                if ($saldo >= 0) {
                    $kredit = $saldo;
                } else {
                    $debit = abs($saldo);
                }
            }

            $totalNsDebit += $debit;
            $totalNsKredit += $kredit;

            $peny_debit = 0;
            $peny_kredit = 0;
            $nsd_debit = $debit;
            $nsd_kredit = $kredit;

            $totalNsdDebit += $nsd_debit;
            $totalNsdKredit += $nsd_kredit;

            $lr_debit = 0;
            $lr_kredit = 0;
            $n_debit = 0;
            $n_kredit = 0;

            if (in_array($akun->tipe_akun, ['pendapatan', 'beban'], true) || in_array(substr($akun->kode_akun, 0, 1), ['4', '5', '6', '7'], true)) {
                $lr_debit = $nsd_debit;
                $lr_kredit = $nsd_kredit;
                $totalLrDebit += $lr_debit;
                $totalLrKredit += $lr_kredit;
            } else {
                $n_debit = $nsd_debit;
                $n_kredit = $nsd_kredit;
                $totalNDebit += $n_debit;
                $totalNKredit += $n_kredit;
            }

            return (object) [
                'kode_akun' => $akun->kode_akun,
                'nama_akun' => $akun->nama_akun,
                'ns_debit' => $debit,
                'ns_kredit' => $kredit,
                'peny_debit' => $peny_debit,
                'peny_kredit' => $peny_kredit,
                'nsd_debit' => $nsd_debit,
                'nsd_kredit' => $nsd_kredit,
                'lr_debit' => $lr_debit,
                'lr_kredit' => $lr_kredit,
                'n_debit' => $n_debit,
                'n_kredit' => $n_kredit,
            ];
        });

        $labaBersih = 0;
        $rugiBersih = 0;

        if ($totalLrKredit > $totalLrDebit) {
            $labaBersih = $totalLrKredit - $totalLrDebit;
        } elseif ($totalLrDebit > $totalLrKredit) {
            $rugiBersih = $totalLrDebit - $totalLrKredit;
        }

        return view('laporan.neraca-saldo', compact(
            'neracaData',
            'totalNsDebit',
            'totalNsKredit',
            'totalNsdDebit',
            'totalNsdKredit',
            'totalLrDebit',
            'totalLrKredit',
            'totalNDebit',
            'totalNKredit',
            'labaBersih',
            'rugiBersih',
            'periode'
        ));
    }
}
