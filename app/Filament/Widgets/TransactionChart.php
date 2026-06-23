<?php

namespace App\Filament\Widgets;

use App\Models\ProdukKeluarEntry;
use App\Models\Inventory;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TransactionChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Transaksi (7 Hari Terakhir)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Example: Charting 'Produk Keluar' (Sales)
        $dataKeluar = Trend::model(ProdukKeluarEntry::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        // Example: Charting 'Inventory' (Masuk)
        $dataMasuk = Trend::model(Inventory::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi Keluar',
                    'data' => $dataKeluar->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(255, 99, 132)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                ],
                [
                    'label' => 'Transaksi Masuk',
                    'data' => $dataMasuk->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                ],
            ],
            'labels' => $dataKeluar->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
