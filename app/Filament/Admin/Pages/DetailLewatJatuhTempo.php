<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DetailLewatJatuhTempo extends Page
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    protected string $view = 'filament.admin.pages.detail-lewat-jatuh-tempo';

    public function getTitle(): string
    {
        return 'Piutang Lewat Jatuh Tempo';
    }

    public function getData(): array
    {
        $today = Carbon::today();

        // Non Konsinyasi — logika identik dengan LaporanUmurPiutang
        // Hitung sisa dari pembayaran aktual, bukan kolom total_terbayar
        $pelanggan = DB::table('penjualan_non_konsinyasi as p')
            ->join('pelanggan as pl', 'p.pelanggan_id', '=', 'pl.id')
            ->leftJoin('pembayaran as py', 'py.penjualan_id', '=', 'p.id')
            ->where('p.jenis_pembayaran', 'kredit')
            ->whereNotNull('p.jatuh_tempo')
            ->where('p.jatuh_tempo', '<', $today->toDateString())
            ->selectRaw("
                pl.namaPelanggan as nama,
                'Pelanggan' as tipe,
                p.no_invoice as referensi,
                p.jatuh_tempo,
                (p.total - COALESCE(SUM(py.jumlah_bayar), 0)) as sisa_piutang,
                DATEDIFF(CURDATE(), p.jatuh_tempo) as hari_terlambat
            ")
            ->groupBy('p.id', 'pl.id', 'pl.namaPelanggan', 'p.no_invoice', 'p.jatuh_tempo', 'p.total')
            ->havingRaw('p.total > COALESCE(SUM(py.jumlah_bayar), 0)')
            ->orderByDesc('hari_terlambat')
            ->get();

        // Konsinyasi — query identik dengan LaporanUmurPiutang (tanpa join ke mitra)
        // Ambil langsung dari tagihan_konsinyasi, join mitra hanya untuk nama
        $tagihanRaw = DB::table('tagihan_konsinyasi as t')
            ->join('laporan_konsinyasi as l', 't.laporan_konsinyasi_id', '=', 'l.id')
            ->join('penjualan_konsinyasi as pk', 'l.penjualan_konsinyasi_id', '=', 'pk.id')
            ->join('mitra as m', 'pk.kode_mitra', '=', 'm.kode_mitra')
            ->where('t.sisa_tagihan', '>', 0)
            ->select('m.namaMitra as nama', 't.no_tagihan as referensi', 't.jatuh_tempo', 't.sisa_tagihan')
            ->get();

        // Filter lewat jatuh tempo menggunakan Carbon — identik dengan LaporanUmurPiutang
        $mitra = $tagihanRaw->filter(function ($t) use ($today) {
            if (!$t->jatuh_tempo) return false;
            $umur = (int) Carbon::parse($t->jatuh_tempo)->diffInDays($today, false);
            return $umur > 0; // sudah lewat jatuh tempo
        })->map(function ($t) use ($today) {
            return (object) [
                'nama'          => $t->nama,
                'tipe'          => 'Mitra',
                'referensi'     => $t->referensi,
                'jatuh_tempo'   => $t->jatuh_tempo,
                'sisa_piutang'  => $t->sisa_tagihan,
                'hari_terlambat'=> (int) Carbon::parse($t->jatuh_tempo)->diffInDays($today, false),
            ];
        });

        return collect($pelanggan)->concat($mitra)
            ->sortByDesc('hari_terlambat')
            ->values()
            ->toArray();
    }
}
