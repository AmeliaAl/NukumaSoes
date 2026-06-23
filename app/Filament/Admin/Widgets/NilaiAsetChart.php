<?php
// app/Filament/Widgets/NilaiAsetChart.php

namespace App\Filament\Admin\Widgets;

use App\Models\Aset;
use Filament\Widgets\ChartWidget;

class NilaiAsetChart extends ChartWidget
{
    protected ?string $heading = 'Nilai Buku Aset per Kategori';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $data = Aset::query()
    ->join('kategori_aset', 'aset.id_kategori', '=', 'kategori_aset.id')
    ->selectRaw('kategori_aset.nama_kategori as kategori, SUM(aset.nilai_perolehan) as nilai_buku')
    ->groupBy('kategori_aset.id', 'kategori_aset.nama_kategori')
    ->get();
        return [
            'datasets' => [
                [
                    'label' => 'Nilai Buku (Rp)',
                    'data' => $data->pluck('nilai_buku')->toArray(),
                    'backgroundColor' => [
                        '#378ADD', '#1D9E75', '#EF9F27', '#D4537E', '#888780',
                    ],
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $data->pluck('kategori')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => [
                    'ticks' => [
                        'callback' => "function(value) { return 'Rp ' + (value/1000000).toFixed(1) + ' jt'; }",
                    ],
                ],
            ],
        ];
    }
}