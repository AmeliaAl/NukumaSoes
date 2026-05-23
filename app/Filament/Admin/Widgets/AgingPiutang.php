<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AgingPiutang extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return 'Aging Piutang';
    }

    protected function getData(): array
    {
        // Pelanggan: aging dihitung dari jatuh_tempo
        $pk = DB::table('penjualan_non_konsinyasi as p')
            ->where('p.jenis_pembayaran', 'kredit')
            ->whereRaw('p.total_terbayar < p.total')
            ->whereNotNull('p.jatuh_tempo')
            ->selectRaw("
                SUM(CASE WHEN DATEDIFF(CURDATE(), p.jatuh_tempo) BETWEEN 0 AND 30  THEN (p.total - p.total_terbayar) ELSE 0 END) as d0_30,
                SUM(CASE WHEN DATEDIFF(CURDATE(), p.jatuh_tempo) BETWEEN 31 AND 60 THEN (p.total - p.total_terbayar) ELSE 0 END) as d31_60,
                SUM(CASE WHEN DATEDIFF(CURDATE(), p.jatuh_tempo) BETWEEN 61 AND 90 THEN (p.total - p.total_terbayar) ELSE 0 END) as d61_90,
                SUM(CASE WHEN DATEDIFF(CURDATE(), p.jatuh_tempo) > 90              THEN (p.total - p.total_terbayar) ELSE 0 END) as d90plus
            ")
            ->first();

        // Mitra: aging dihitung dari tanggal_tagihan (tidak ada jatuh tempo tetap)
        $mt = DB::table('tagihan_konsinyasi as t')
            ->where('t.status', 'BELUM LUNAS')
            ->selectRaw("
                SUM(CASE WHEN DATEDIFF(CURDATE(), t.tanggal_tagihan) BETWEEN 0 AND 30  THEN t.sisa_tagihan ELSE 0 END) as d0_30,
                SUM(CASE WHEN DATEDIFF(CURDATE(), t.tanggal_tagihan) BETWEEN 31 AND 60 THEN t.sisa_tagihan ELSE 0 END) as d31_60,
                SUM(CASE WHEN DATEDIFF(CURDATE(), t.tanggal_tagihan) BETWEEN 61 AND 90 THEN t.sisa_tagihan ELSE 0 END) as d61_90,
                SUM(CASE WHEN DATEDIFF(CURDATE(), t.tanggal_tagihan) > 90              THEN t.sisa_tagihan ELSE 0 END) as d90plus
            ")
            ->first();

        return [
            'datasets' => [
                [
                    'data' => [
                        (float)($pk->d0_30   ?? 0) + (float)($mt->d0_30   ?? 0),
                        (float)($pk->d31_60  ?? 0) + (float)($mt->d31_60  ?? 0),
                        (float)($pk->d61_90  ?? 0) + (float)($mt->d61_90  ?? 0),
                        (float)($pk->d90plus ?? 0) + (float)($mt->d90plus ?? 0),
                    ],
                    'backgroundColor' => [
                        '#22c55e',
                        '#f59e0b',
                        '#f97316',
                        '#ef4444',
                    ],
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => [
                '0–30 hari (Lancar)',
                '31–60 hari (Waspada)',
                '61–90 hari (Perlu Perhatian)',
                '>90 hari (Risiko Tinggi)',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
