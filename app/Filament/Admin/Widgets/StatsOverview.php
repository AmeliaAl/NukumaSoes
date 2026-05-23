<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();

        // ── Total Piutang (semua sisa belum lunas) ────────────────────────
        $piutangNK = (float) DB::table('penjualan_non_konsinyasi')
            ->where('jenis_pembayaran', 'kredit')
            ->whereRaw('total_terbayar < total')
            ->selectRaw('SUM(total - total_terbayar) as sisa')
            ->value('sisa');

        $piutangKonsinyasi = (float) DB::table('tagihan_konsinyasi')
            ->where('status', 'BELUM LUNAS')
            ->sum('sisa_tagihan');

        $totalPiutang = $piutangNK + $piutangKonsinyasi;

        // ── Piutang Jatuh Tempo (dalam 7 hari ke depan, belum lewat) ─────
        $jatuhTempoNK = (float) DB::table('penjualan_non_konsinyasi')
            ->where('jenis_pembayaran', 'kredit')
            ->whereRaw('total_terbayar < total')
            ->whereNotNull('jatuh_tempo')
            ->whereBetween('jatuh_tempo', [
                $today->toDateString(),
                $today->copy()->addDays(7)->toDateString(),
            ])
            ->selectRaw('SUM(total - total_terbayar) as sisa')
            ->value('sisa');

        $jatuhTempoKonsinyasi = (float) DB::table('tagihan_konsinyasi')
            ->where('status', 'BELUM LUNAS')
            ->whereBetween('jatuh_tempo', [
                $today->toDateString(),
                $today->copy()->addDays(7)->toDateString(),
            ])
            ->sum('sisa_tagihan');

        $totalJatuhTempo = $jatuhTempoNK + $jatuhTempoKonsinyasi;

        // ── Piutang Lewat Jatuh Tempo ─────────────────────────────────────
        $overdueNK = (float) DB::table('penjualan_non_konsinyasi')
            ->where('jenis_pembayaran', 'kredit')
            ->whereRaw('total_terbayar < total')
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '<', $today->toDateString())
            ->selectRaw('SUM(total - total_terbayar) as sisa')
            ->value('sisa');

        $overdueKonsinyasi = (float) DB::table('tagihan_konsinyasi')
            ->where('status', 'BELUM LUNAS')
            ->where('jatuh_tempo', '<', $today->toDateString())
            ->sum('sisa_tagihan');

        $totalOverdue = $overdueNK + $overdueKonsinyasi;

        return [
            Stat::make('Total Piutang', 'Rp ' . number_format($totalPiutang, 0, ',', '.'))
                ->description('Semua sisa piutang belum lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Jatuh Tempo (7 hari)', 'Rp ' . number_format($totalJatuhTempo, 0, ',', '.'))
                ->description('Mendekati tanggal jatuh tempo — klik untuk detail')
                ->descriptionIcon('heroicon-m-clock')
                ->color($totalJatuhTempo > 0 ? 'warning' : 'success')
                ->url(\App\Filament\Admin\Pages\DetailJatuhTempo::getUrl()),

            Stat::make('Lewat Jatuh Tempo', 'Rp ' . number_format($totalOverdue, 0, ',', '.'))
                ->description('Sudah melewati tanggal jatuh tempo — klik untuk detail')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalOverdue > 0 ? 'danger' : 'success')
                ->url(\App\Filament\Admin\Pages\DetailLewatJatuhTempo::getUrl()),
        ];
    }
}
