<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DetailSemuaPiutang extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected string $view = 'filament.admin.pages.detail-semua-piutang';

    public function getTitle(): string
    {
        return 'Semua Piutang Belum Lunas';
    }

    public function getData(): array
    {
        $today = Carbon::today();

        // Non Konsinyasi — kredit, belum lunas
        $pelanggan = DB::table('penjualan_non_konsinyasi as p')
            ->join('pelanggan as pl', 'p.pelanggan_id', '=', 'pl.id')
            ->where('p.jenis_pembayaran', 'kredit')
            ->whereRaw('p.total_terbayar < p.total')
            ->selectRaw("
                pl.namaPelanggan as nama,
                'Pelanggan' as tipe,
                p.no_invoice as referensi,
                p.jatuh_tempo,
                (p.total - p.total_terbayar) as sisa_piutang,
                CASE
                    WHEN p.jatuh_tempo IS NULL THEN 'Belum ada jatuh tempo'
                    WHEN p.jatuh_tempo < CURDATE() THEN 'Lewat Jatuh Tempo'
                    WHEN DATEDIFF(p.jatuh_tempo, CURDATE()) <= 7 THEN 'Mendekati Jatuh Tempo'
                    ELSE 'Belum Jatuh Tempo'
                END as status_piutang
            ")
            ->orderBy('p.jatuh_tempo')
            ->get();

        // Konsinyasi — tagihan belum lunas
        $mitra = DB::table('tagihan_konsinyasi as t')
            ->join('laporan_konsinyasi as l', 't.laporan_konsinyasi_id', '=', 'l.id')
            ->join('penjualan_konsinyasi as pk', 'l.penjualan_konsinyasi_id', '=', 'pk.id')
            ->join('mitra as m', 'pk.kode_mitra', '=', 'm.kode_mitra')
            ->where('t.sisa_tagihan', '>', 0)
            ->selectRaw("
                m.namaMitra as nama,
                'Mitra' as tipe,
                t.no_tagihan as referensi,
                t.jatuh_tempo,
                t.sisa_tagihan as sisa_piutang,
                CASE
                    WHEN t.jatuh_tempo IS NULL THEN 'Belum ada jatuh tempo'
                    WHEN t.jatuh_tempo < CURDATE() THEN 'Lewat Jatuh Tempo'
                    WHEN DATEDIFF(t.jatuh_tempo, CURDATE()) <= 7 THEN 'Mendekati Jatuh Tempo'
                    ELSE 'Belum Jatuh Tempo'
                END as status_piutang
            ")
            ->orderBy('t.jatuh_tempo')
            ->get();

        return collect($pelanggan)->concat($mitra)
            ->sortBy(fn ($row) => $row->jatuh_tempo ?? '9999-99-99')
            ->values()
            ->toArray();
    }
}
