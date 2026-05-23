<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class TopSlowPiutang extends Widget
{
    protected static ?int $sort = 3;
    protected static bool $isDiscovered = false;
    protected int | string | array $columnSpan = 'full';
    protected string $view = 'filament.admin.widgets.top-slow-piutang';

    public function render(): View
    {
        return view('filament.admin.widgets.top-slow-piutang', [
            'rows' => $this->getData(),
        ]);
    }

    protected function getData(): \Illuminate\Support\Collection
    {
        $pelanggan = DB::table('penjualan_non_konsinyasi as p')
            ->join('pelanggan as pl', 'p.pelanggan_id', '=', 'pl.id')
            ->where('p.jenis_pembayaran', 'kredit')
            ->whereRaw('p.total_terbayar < p.total')
            ->selectRaw("pl.namaPelanggan as nama, 'Pelanggan' as tipe, SUM(p.total) as total_transaksi, SUM(p.total - p.total_terbayar) as sisa_piutang")
            ->groupBy('pl.id', 'pl.namaPelanggan')
            ->having('sisa_piutang', '>', 0)
            ->get();

        $mitra = DB::table('tagihan_konsinyasi as t')
            ->join('laporan_konsinyasi as l', 't.laporan_konsinyasi_id', '=', 'l.id')
            ->join('penjualan_konsinyasi as pk', 'l.penjualan_konsinyasi_id', '=', 'pk.id')
            ->join('mitra as m', 'pk.kode_mitra', '=', 'm.kode_mitra')
            ->where('t.status', 'BELUM LUNAS')
            ->selectRaw("m.namaMitra as nama, 'Mitra' as tipe, SUM(t.total_tagihan) as total_transaksi, SUM(t.sisa_tagihan) as sisa_piutang")
            ->groupBy('m.id', 'm.namaMitra')
            ->having('sisa_piutang', '>', 0)
            ->get();

        return $pelanggan->concat($mitra)
            ->map(function ($row) {
                $turnover = $row->sisa_piutang > 0
                    ? round($row->total_transaksi / $row->sisa_piutang, 2)
                    : 0;
                $dso = $turnover > 0 ? round(365 / $turnover) : 0;
                $status = $turnover > 3 ? 'Baik' : ($turnover >= 1 ? 'Cukup' : 'Buruk');

                return (object) [
                    'nama'        => $row->nama,
                    'tipe'        => $row->tipe,
                    'sisa_piutang'=> $row->sisa_piutang,
                    'turnover'    => $turnover,
                    'dso'         => $dso,
                    'status'      => $status,
                ];
            })
            ->sortBy('turnover')
            ->take(5)
            ->values();
    }
}
