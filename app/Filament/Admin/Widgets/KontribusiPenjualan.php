<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class KontribusiPenjualan extends ChartWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return 'Kontribusi Penjualan';
    }

    protected function getData(): array
    {
        $nonKonsinyasi = (float) DB::table('penjualan_non_konsinyasi')
            ->sum('total');

        $konsinyasi = (float) DB::table('laporan_konsinyasi')
            ->sum('total_laporan');

        $total = $nonKonsinyasi + $konsinyasi;

        $pctNK = $total > 0 ? round($nonKonsinyasi / $total * 100, 1) : 0;
        $pctKS = $total > 0 ? round($konsinyasi    / $total * 100, 1) : 0;

        return [
            'datasets' => [
                [
                    'data' => [$nonKonsinyasi, $konsinyasi],
                    'backgroundColor' => ['#3b82f6', '#10b981'],
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => [
                'Non Konsinyasi (' . $pctNK . '%)',
                'Konsinyasi ('    . $pctKS . '%)',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'bottom'],
                'tooltip' => [
                    'callbacks' => [
                        // Format tooltip via JS tidak bisa dari PHP,
                        // tapi label sudah menyertakan persentase
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
