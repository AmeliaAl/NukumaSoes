<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dasbor Aset';
    protected static ?string $navigationLabel = 'Dasbor';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\AsetStatsOverview::class,
            \App\Filament\Admin\Widgets\DetailPenyusutanAsetWidget::class,
            \App\Filament\Widgets\NilaiAsetChart::class,
            \App\Filament\Widgets\StatusAsetChart::class,
\App\Filament\Admin\Widgets\PerolehanTerbaruWidget::class, 
            \App\Filament\Widgets\PenyusutanWidget::class,
            \App\Filament\Widgets\NotifikasiWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 3;
    }
}