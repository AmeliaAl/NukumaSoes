<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Aset;
use App\Models\Penyusutan;

class AsetStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $bulanIni = now();
        $bulanLalu = now()->copy()->subMonth();

        $totalAset = Aset::count();
        $nilaiPerolehan = Aset::sum('nilai_perolehan');
    

        $perluPemeliharaan = Aset::with(['kategori_aset', 'pemeliharaan'])
    ->get()
    ->filter(fn ($aset) => $aset->isButuhPemeliharaan(7))
    ->filter(function ($aset) {
        $next = $aset->getNextPemeliharaan();

        if (! $next) {
            return false;
        }

        if (empty($aset->reminder)) {
            return true;
        }

        return \Carbon\Carbon::parse($aset->reminder)->format('Y-m-d') !== $next->format('Y-m-d');
    })
    ->count();
        $totalAsetBulanIni = Aset::whereMonth('tanggal_perolehan', $bulanIni->month)
            ->whereYear('tanggal_perolehan', $bulanIni->year)
            ->count();

        $totalAsetBulanLalu = Aset::whereMonth('tanggal_perolehan', $bulanLalu->month)
            ->whereYear('tanggal_perolehan', $bulanLalu->year)
            ->count();

        $selisihAset = $totalAsetBulanIni - $totalAsetBulanLalu;

        $nilaiBulanIni = Aset::whereMonth('tanggal_perolehan', $bulanIni->month)
            ->whereYear('tanggal_perolehan', $bulanIni->year)
            ->sum('nilai_perolehan');

        $nilaiBulanLalu = Aset::whereMonth('tanggal_perolehan', $bulanLalu->month)
            ->whereYear('tanggal_perolehan', $bulanLalu->year)
            ->sum('nilai_perolehan');

        $selisihNilai = $nilaiBulanIni - $nilaiBulanLalu;

        
        return [
            Stat::make('Total Aset Tetap', $totalAset)
                ->description(
                    $selisihAset > 0
                        ? '+' . $selisihAset . ' dari bulan lalu'
                        : ($selisihAset < 0
                            ? '-' . abs($selisihAset) . ' dari bulan lalu'
                            : 'Sama dengan bulan lalu')
                )
                ->descriptionIcon(
                    $selisihAset > 0
                        ? 'heroicon-m-arrow-trending-up'
                        : ($selisihAset < 0
                            ? 'heroicon-m-arrow-trending-down'
                            : 'heroicon-m-minus')
                )
                ->color(
                    $selisihAset > 0
                        ? 'success'
                        : ($selisihAset < 0 ? 'danger' : 'gray')
                )
                ->icon('heroicon-o-squares-2x2'),

            Stat::make('Nilai Perolehan', 'Rp ' . number_format($nilaiPerolehan, 0, ',', '.'))
                ->description(
                    ($selisihNilai >= 0 ? '+' : '-') .
                    'Rp ' . number_format(abs($selisihNilai), 0, ',', '.') .
                    ' dari bulan lalu'
                )
                ->descriptionIcon(
                    $selisihNilai >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($selisihNilai >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-banknotes'),

          

            Stat::make('Perlu Pemeliharaan', $perluPemeliharaan)
                //->description('Aset')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($perluPemeliharaan > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-wrench-screwdriver'),
        ];
    }
}