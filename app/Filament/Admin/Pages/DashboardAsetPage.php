<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class DashboardAsetPage extends BaseDashboard
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $title = 'Dasbor Aset';
    protected static ?string $navigationLabel = 'Dasbor Aset';
    protected static \UnitEnum|string|null $navigationGroup = 'Aset';
    
    protected static string $routePath = 'dasbor-aset';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\AsetStatsOverview::class,
            \App\Filament\Admin\Widgets\DetailPenyusutanAset::class,
            \App\Filament\Admin\Widgets\NilaiAsetChart::class,
            \App\Filament\Admin\Widgets\PerolehanTerbaruTable::class, 
            \App\Filament\Admin\Widgets\PenyusutanWidget::class,
            \App\Filament\Admin\Widgets\NotifikasiWidget::class,
            \App\Filament\Admin\Widgets\ReminderPemeliharaanWidget::class,
        ];
    }
}
