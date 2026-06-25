<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PenjualanNonKonsinyasi;
use App\Models\LaporanKonsinyasi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class GrafikPenjualan extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '250px';

    public function getHeading(): string
    {
        return 'Grafik Penjualan';
    }

    protected function getFilters(): ?array
    {
        $tahunAwal = 2024;
        $tahunAkhir = (int) Carbon::now()->format('Y');
        $filters = [];
        for ($y = $tahunAkhir; $y >= $tahunAwal; $y--) {
            $filters[(string) $y] = (string) $y;
        }
        return $filters;
    }

    protected function getData(): array
    {
        $tahun = (int) ($this->filter ?? Carbon::now()->format('Y'));

        $bulan = [];
        $nonKonsinyasi = [];
        $konsinyasi = [];

        for ($m = 1; $m <= 12; $m++) {
            $bulan[] = Carbon::create($tahun, $m, 1)->translatedFormat('M');

            $nonKonsinyasi[] = (int) PenjualanNonKonsinyasi::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $m)
                ->sum('total');

            $konsinyasi[] = (int) LaporanKonsinyasi::whereYear('tanggal_laporan', $tahun)
                ->whereMonth('tanggal_laporan', $m)
                ->sum('total_laporan');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Non Konsinyasi',
                    'data'            => $nonKonsinyasi,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Konsinyasi',
                    'data'            => $konsinyasi,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $bulan,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 100000,
                    ],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
