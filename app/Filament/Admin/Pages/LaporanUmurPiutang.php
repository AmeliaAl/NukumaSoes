<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanUmurPiutang extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Umur Piutang';
    protected static \UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.admin.pages.laporan-umur-piutang';

    public function getTitle(): string
    {
        return 'Laporan ';
    }

    public function getData(): array
    {
        $today = Carbon::today();
        $rows  = [];

        $penjualans = DB::table('penjualan_non_konsinyasi as p')
            ->join('pelanggan as pl', 'p.pelanggan_id', '=', 'pl.id')
            ->leftJoin('pembayaran as py', 'py.penjualan_id', '=', 'p.id')
            ->selectRaw('pl.id as pihak_id, pl.namaPelanggan as nama, p.no_invoice as referensi, p.jatuh_tempo, p.total, COALESCE(SUM(py.jumlah_bayar), 0) as total_bayar')
            ->groupBy('p.id', 'pl.id', 'pl.namaPelanggan', 'p.no_invoice', 'p.jatuh_tempo', 'p.total')
            ->havingRaw('p.total > COALESCE(SUM(py.jumlah_bayar), 0)')
            ->get();

        foreach ($penjualans as $p) {
            $piutang = (int) $p->total - (int) $p->total_bayar;
            $umur = $p->jatuh_tempo ? (int) Carbon::parse($p->jatuh_tempo)->diffInDays($today, false) : 0;
            $rows[] = ['pihak_id' => 'NK_' . $p->pihak_id, 'nama' => $p->nama, 'referensi' => $p->referensi, 'piutang' => $piutang, 'kolom' => $this->getKolom($umur)];
        }

        $tagihans = DB::table('tagihan_konsinyasi as t')
            ->join('laporan_konsinyasi as l', 't.laporan_konsinyasi_id', '=', 'l.id')
            ->join('penjualan_konsinyasi as pk', 'l.penjualan_konsinyasi_id', '=', 'pk.id')
            ->join('mitra as m', 'pk.kode_mitra', '=', 'm.kode_mitra')
            ->select('m.id as pihak_id', 'm.namaMitra as nama', 't.no_tagihan as referensi', 't.jatuh_tempo', 't.sisa_tagihan as piutang')
            ->where('t.sisa_tagihan', '>', 0)
            ->get();

        foreach ($tagihans as $t) {
            $umur = $t->jatuh_tempo ? (int) Carbon::parse($t->jatuh_tempo)->diffInDays($today, false) : 0;
            $rows[] = ['pihak_id' => 'KSY_' . $t->pihak_id, 'nama' => $t->nama, 'referensi' => $t->referensi, 'piutang' => (int) $t->piutang, 'kolom' => $this->getKolom($umur)];
        }

        $grouped = [];
        foreach ($rows as $row) {
            $key = $row['pihak_id'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['nama' => $row['nama'], 'items' => [], 'subtotal' => $this->emptyKolom()];
            }
            $item = $this->emptyKolom();
            $item['referensi']   = $row['referensi'];
            $item[$row['kolom']] = $row['piutang'];
            $item['total']       = $row['piutang'];
            $grouped[$key]['items'][] = $item;
            $grouped[$key]['subtotal'][$row['kolom']] += $row['piutang'];
            $grouped[$key]['subtotal']['total']        += $row['piutang'];
        }

        $grandTotal = $this->emptyKolom();
        foreach ($grouped as $g) {
            foreach (array_keys($this->emptyKolom()) as $k) {
                if ($k === 'referensi') continue;
                $grandTotal[$k] += $g['subtotal'][$k];
            }
        }

        return ['grouped' => $grouped, 'grandTotal' => $grandTotal, 'tanggal' => $today->translatedFormat('d F Y')];
    }

    private function getKolom(int $umur): string
    {
        if ($umur <= 0)  return 'belum_jatuh';
        if ($umur <= 30) return 'hari_1_30';
        if ($umur <= 60) return 'hari_31_60';
        if ($umur <= 90) return 'hari_61_90';
        return 'hari_90plus';
    }

    private function emptyKolom(): array
    {
        return ['referensi' => '', 'belum_jatuh' => 0, 'hari_1_30' => 0, 'hari_31_60' => 0, 'hari_61_90' => 0, 'hari_90plus' => 0, 'total' => 0];
    }
}