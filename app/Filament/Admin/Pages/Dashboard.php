<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dasbor Aset';
    protected static ?string $navigationLabel = 'Dasbor';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\AsetStatsOverview::class,
            \App\Filament\Admin\Widgets\DetailPenyusutanAset::class,
            \App\Filament\Admin\Widgets\NilaiAsetChart::class,
            \App\Filament\Admin\Widgets\PerolehanTerbaruTable::class, 
            \App\Filament\Admin\Widgets\PenyusutanWidget::class,
            \App\Filament\Admin\Widgets\NotifikasiWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 3;
    }
}