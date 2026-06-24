<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class DashboardPenjualan extends BaseDashboard
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Dasbor Penjualan';
    protected static ?string $navigationLabel = 'Dasbor Penjualan';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\StatsOverview::class,
            \App\Filament\Admin\Widgets\GrafikPenjualan::class,
            \App\Filament\Admin\Widgets\KontribusiPenjualan::class,
            \App\Filament\Admin\Widgets\AgingPiutang::class,
            \App\Filament\Admin\Widgets\TopMenunggak::class,
            
        ];
    }
}
