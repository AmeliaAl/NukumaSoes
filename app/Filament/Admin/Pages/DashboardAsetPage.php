<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Aset;
use App\Models\KategoriAset;
use App\Models\LokasiAset;
use BackedEnum;
use UnitEnum;
use App\Models\Penyusutan;

class DashboardAsetPage extends Page
{
   protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dasbor Aset';
    protected static ?string $navigationLabel = 'Dasbor';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';

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

}
