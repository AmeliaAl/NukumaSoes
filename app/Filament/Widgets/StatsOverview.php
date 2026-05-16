<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Produk', Product::count())
                ->description('Semua sku produk')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Produk Aman', Product::where('status', 'aman')->count())
                ->description('Stok aman')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Hampir Expired', Product::where('status', 'mau_expired')->count())
                ->description('Kurang dari 30 hari')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make('Produk Expired', Product::where('status', 'expired')->count())
                ->description('Sudah kadaluarsa')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
