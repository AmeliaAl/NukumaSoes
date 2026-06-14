<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Pembelian;
use App\Models\Overhead;
use Barryvdh\DomPDF\Facade\Pdf;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'periode_awal' => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
            'akun_id' => 'nullable|string',
        ]);

        $data = $this->buildBukuBesar($request);

        return view('buku_besar.index', $data);
    }

    public function print(Request $request)
    {
        $request->validate([
            'periode_awal' => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
            'akun_id' => 'nullable|string',
        ]);

        $data = $this->buildBukuBesar($request);

        $pdf = Pdf::loadView('buku_besar.print', $data);

        return $pdf->download('buku-besar.pdf');
    }

    public function export(Request $request)
    {
        $request->validate([
            'periode_awal' => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
            'akun_id' => 'nullable|string',
        ]);

        $data = $this->buildBukuBesar($request);

        $jurnals = $data['jurnals'];
        $totalDebit = $data['totalDebit'];
        $totalKredit = $data['totalKredit'];
        $saldoAkhir = $data['saldoAkhir'];
        $akun = $data['akun'];
        $periodeAwal = $data['periodeAwal'];
        $periodeAkhir = $data['periodeAkhir'];
        $fileName = 'buku-besar-' . ($akun?->kode_akun ?? 'all') . '-' . now()->format('YmdHis') . '.xlsx';

        $callback = function () use ($jurnals, $totalDebit, $totalKredit, $saldoAkhir, $akun, $periodeAwal, $periodeAkhir) {
            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile('php://output');

            $writer->addRow(WriterEntityFactory::createRowFromArray([
                'Kode Akun',
                $akun?->kode_akun ?? '',
            ]));
            $writer->addRow(WriterEntityFactory::createRowFromArray([
                'Nama Akun',
                $akun?->nama_akun ?? '',
            ]));
            $writer->addRow(WriterEntityFactory::createRowFromArray([
                'Periode',
                ($periodeAwal ?? '-') . ' s/d ' . ($periodeAkhir ?? '-'),
            ]));
            $writer->addRow(WriterEntityFactory::createRowFromArray([]));

            $writer->addRow(WriterEntityFactory::createRowFromArray([
                'No',
                'Tanggal',
                'Bukti',
                'Keterangan',
                'Ref',
                'Debit',
                'Kredit',
                'Saldo',
            ]));

            foreach ($jurnals as $index => $jurnal) {
                $writer->addRow(WriterEntityFactory::createRowFromArray([
                    $index + 1,
                    $jurnal['tanggal'],
                    $jurnal['bukti'],
                    $jurnal['keterangan'],
                    $jurnal['ref'],
                    $jurnal['debit'],
                    $jurnal['kredit'],
                    $jurnal['saldo'],
                ]));
            }

            $writer->addRow(WriterEntityFactory::createRowFromArray([]));
            $writer->addRow(WriterEntityFactory::createRowFromArray([
                '',
                '',
                '',
                '',
                'TOTAL',
                $totalDebit,
                $totalKredit,
                $saldoAkhir,
            ]));

            $writer->close();
        };

        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function buildBukuBesar(Request $request)
    {
        $coas = Coa::all();
        $akunId = $request->akun_id;
        $periodeAwal = $request->periode_awal;
        $periodeAkhir = $request->periode_akhir;
        $jurnals = [];

        $pembelians = Pembelian::query();

        if ($periodeAwal && $periodeAkhir) {
            $pembelians->whereBetween('tanggal', [
                $periodeAwal,
                $periodeAkhir,
            ]);
        }

        foreach ($pembelians->get() as $pembelian) {
            $noBukti = 'PB-' . str_pad($pembelian->id, 3, '0', STR_PAD_LEFT);
            $subtotal = $pembelian->subtotal ?: ($pembelian->qty * $pembelian->harga);
            $diskon = $pembelian->diskon ?? 0;
            $ongkir = $pembelian->ongkir ?? 0;
            $totalBersih = $pembelian->total_bersih ?? ($subtotal - $diskon);
            $grandTotal = $pembelian->grand_total ?? ($totalBersih + $ongkir);

            if ($akunId == 552) {
                $jurnals[] = [
                    'tanggal' => $pembelian->tanggal,
                    'bukti' => $noBukti,
                    'keterangan' => 'Pembelian ' . $pembelian->bahanBaku->nama_bahan,
                    'ref' => '552',
                    'debit' => $subtotal,
                    'kredit' => 0,
                ];
            }

            if ($akunId == 553 && $ongkir > 0) {
                $jurnals[] = [
                    'tanggal' => $pembelian->tanggal,
                    'bukti' => $noBukti,
                    'keterangan' => 'Ongkos Angkut Pembelian',
                    'ref' => '553',
                    'debit' => $ongkir,
                    'kredit' => 0,
                ];
            }

            if ($akunId == 554 && $diskon > 0) {
                $jurnals[] = [
                    'tanggal' => $pembelian->tanggal,
                    'bukti' => $noBukti,
                    'keterangan' => 'Potongan Pembelian',
                    'ref' => '554',
                    'debit' => 0,
                    'kredit' => $diskon,
                ];
            }

            if ($akunId == $pembelian->coa->kode_akun) {
                $keterangan = '';
                if (str_contains(strtolower($pembelian->coa->nama_akun), 'kas')) {
                    $keterangan = 'Keluar Kas Kecil';
                } elseif (str_contains(strtolower($pembelian->coa->nama_akun), 'bank')) {
                    $keterangan = 'Pembayaran Bank';
                } else {
                    $keterangan = 'Hutang Pembelian';
                }

                $jurnals[] = [
                    'tanggal' => $pembelian->tanggal,
                    'bukti' => $noBukti,
                    'keterangan' => $keterangan,
                    'ref' => $pembelian->coa->kode_akun,
                    'debit' => 0,
                    'kredit' => $grandTotal,
                ];
            }
        }

        $overheads = Overhead::query();

        if ($periodeAwal && $periodeAkhir) {
            $overheads->whereBetween('tanggal', [
                $periodeAwal,
                $periodeAkhir,
            ]);
        }

        foreach ($overheads->get() as $overhead) {
            $noBukti = 'OH-' . str_pad($overhead->id, 3, '0', STR_PAD_LEFT);

            if ($akunId == $overhead->coa->kode_akun) {
                $jurnals[] = [
                    'tanggal' => $overhead->tanggal,
                    'bukti' => $noBukti,
                    'keterangan' => $overhead->keterangan,
                    'ref' => $overhead->coa->kode_akun,
                    'debit' => $overhead->nominal,
                    'kredit' => 0,
                ];
            }

            $paymentCoa = $overhead->paymentCoa;
            if ($akunId == ($paymentCoa->kode_akun ?? 111)) {
                $jurnals[] = [
                    'tanggal' => $overhead->tanggal,
                    'bukti' => $noBukti,
                    'keterangan' => 'Pembayaran Overhead',
                    'ref' => $paymentCoa->kode_akun ?? '111',
                    'debit' => 0,
                    'kredit' => $overhead->nominal,
                ];
            }
        }

        usort($jurnals, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        $saldo = 0;
        foreach ($jurnals as $key => $jurnal) {
            $saldo += $jurnal['debit'];
            $saldo -= $jurnal['kredit'];
            $jurnals[$key]['saldo'] = $saldo;
        }

        $totalDebit = collect($jurnals)->sum('debit');
        $totalKredit = collect($jurnals)->sum('kredit');
        $saldoAkhir = $saldo;
        $akun = Coa::where('kode_akun', $akunId)->first();

        return [
            'coas' => $coas,
            'akun' => $akun,
            'jurnals' => $jurnals,
            'periodeAwal' => $periodeAwal,
            'periodeAkhir' => $periodeAkhir,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
            'saldoAkhir' => $saldoAkhir,
        ];
    }
}
