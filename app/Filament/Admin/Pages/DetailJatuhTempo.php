<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DetailJatuhTempo extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected string $view = 'filament.admin.pages.detail-jatuh-tempo';

    public function getTitle(): string
    {
        return 'Piutang Mendekati Jatuh Tempo (7 Hari)';
    }

    public function getData(): array
    {
        $today = Carbon::today();
        $batas = $today->copy()->addDays(7);

        // Non Konsinyasi
        $pelanggan = DB::table('penjualan_non_konsinyasi as p')
            ->join('pelanggan as pl', 'p.pelanggan_id', '=', 'pl.id')
            ->where('p.jenis_pembayaran', 'kredit')
            ->whereRaw('p.total_terbayar < p.total')
            ->whereNotNull('p.jatuh_tempo')
            ->whereBetween('p.jatuh_tempo', [$today->toDateString(), $batas->toDateString()])
            ->selectRaw("
                pl.namaPelanggan as nama,
                'Pelanggan' as tipe,
                p.no_invoice as referensi,
                p.jatuh_tempo,
                (p.total - p.total_terbayar) as sisa_piutang,
                DATEDIFF(p.jatuh_tempo, CURDATE()) as sisa_hari
            ")
            ->orderBy('p.jatuh_tempo')
            ->get();

        // Konsinyasi
        $mitra = DB::table('tagihan_konsinyasi as t')
            ->join('laporan_konsinyasi as l', 't.laporan_konsinyasi_id', '=', 'l.id')
            ->join('penjualan_konsinyasi as pk', 'l.penjualan_konsinyasi_id', '=', 'pk.id')
            ->join('mitra as m', 'pk.kode_mitra', '=', 'm.kode_mitra')
            ->where('t.status', 'BELUM LUNAS')
            ->whereBetween('t.jatuh_tempo', [$today->toDateString(), $batas->toDateString()])
            ->selectRaw("
                m.namaMitra as nama,
                'Mitra' as tipe,
                t.no_tagihan as referensi,
                t.jatuh_tempo,
                t.sisa_tagihan as sisa_piutang,
                DATEDIFF(t.jatuh_tempo, CURDATE()) as sisa_hari
            ")
            ->orderBy('t.jatuh_tempo')
            ->get();

        return collect($pelanggan)->concat($mitra)
            ->sortBy('jatuh_tempo')
            ->values()
            ->toArray();
    }
}
