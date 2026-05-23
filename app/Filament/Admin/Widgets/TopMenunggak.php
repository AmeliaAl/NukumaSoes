<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class TopMenunggak extends Widget
{
    protected static ?int $sort = 4;
    protected static bool $isDiscovered = false;
    protected int | string | array $columnSpan = 'full';
    protected string $view = 'filament.admin.widgets.top-menunggak';

    public function render(): View
    {
        return view('filament.admin.widgets.top-menunggak', [
            'rows' => $this->getData(),
        ]);
    }

    protected function getData(): \Illuminate\Support\Collection
    {
        // Pelanggan: umur dari jatuh_tempo, ambil per transaksi belum lunas
        $pelanggan = DB::table('penjualan_non_konsinyasi as p')
            ->join('pelanggan as pl', 'p.pelanggan_id', '=', 'pl.id')
            ->where('p.jenis_pembayaran', 'kredit')
            ->whereRaw('p.total_terbayar < p.total')
            ->whereNotNull('p.jatuh_tempo')
            ->selectRaw("
                pl.namaPelanggan as nama,
                'Pelanggan' as tipe,
                SUM(p.total - p.total_terbayar) as sisa_piutang,
                MAX(DATEDIFF(CURDATE(), p.jatuh_tempo)) as umur_piutang
            ")
            ->groupBy('pl.id', 'pl.namaPelanggan')
            ->having('sisa_piutang', '>', 0)
            ->get();

        // Mitra: umur dari tanggal_tagihan
        $mitra = DB::table('tagihan_konsinyasi as t')
            ->join('laporan_konsinyasi as l', 't.laporan_konsinyasi_id', '=', 'l.id')
            ->join('penjualan_konsinyasi as pk', 'l.penjualan_konsinyasi_id', '=', 'pk.id')
            ->join('mitra as m', 'pk.kode_mitra', '=', 'm.kode_mitra')
            ->where('t.status', 'BELUM LUNAS')
            ->selectRaw("
                m.namaMitra as nama,
                'Mitra' as tipe,
                SUM(t.sisa_tagihan) as sisa_piutang,
                MAX(DATEDIFF(CURDATE(), t.tanggal_tagihan)) as umur_piutang
            ")
            ->groupBy('m.id', 'm.namaMitra')
            ->having('sisa_piutang', '>', 0)
            ->get();

        return $pelanggan->concat($mitra)
            ->map(function ($row) {
                $umur = max((int) $row->umur_piutang, 0);

                if ($umur <= 30) {
                    $kategori = 'Lancar';
                } elseif ($umur <= 60) {
                    $kategori = 'Waspada';
                } elseif ($umur <= 90) {
                    $kategori = 'Perlu Perhatian';
                } else {
                    $kategori = 'Risiko Tinggi';
                }

                return (object) [
                    'nama'         => $row->nama,
                    'tipe'         => $row->tipe,
                    'sisa_piutang' => (float) $row->sisa_piutang,
                    'umur_piutang' => $umur,
                    'kategori'     => $kategori,
                ];
            })
            ->sortByDesc('umur_piutang')
            ->sortByDesc('sisa_piutang')
            ->sortByDesc('umur_piutang')
            ->take(5)
            ->values();
    }
}
