<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Mitra;
use App\Models\Pelanggan;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Barang', Barang::count())
                ->description('Jumlah data barang')
                ->color('primary'),

            Stat::make('Total Kategori', Kategori::count())
                ->color('info'),

            Stat::make('Total Mitra', Mitra::count())
                ->color('success'),

            Stat::make('Total Pelanggan', Pelanggan::count())
                ->color('warning'),
        ];
    }
}
