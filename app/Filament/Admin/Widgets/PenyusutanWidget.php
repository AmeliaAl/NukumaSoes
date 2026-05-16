<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Penyusutan;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class PenyusutanWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.penyusutan-widget';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    protected function getViewData(): array
    {
        $periode = now()->format('Y-m');

        $rows = Penyusutan::query()
            ->join('aset', 'penyusutan.aset_id', '=', 'aset.id')
            ->join('kategori_aset', 'aset.id_kategori', '=', 'kategori_aset.id')
            ->where('penyusutan.periode', $periode)
            ->select(
                'kategori_aset.nama_kategori as kategori',
                DB::raw('SUM(penyusutan.beban_penyusutan) as total')
            )
            ->groupBy('kategori_aset.id', 'kategori_aset.nama_kategori')
            ->orderByDesc('total')
            ->get();

        $grandTotal = (float) $rows->sum('total');

        $palette = [
            '#3b82f6',
            '#22c55e',
            '#f59e0b',
            '#ec4899',
            '#6b7280',
            '#8b5cf6',
            '#14b8a6',
        ];

        $items = $rows->values()->map(function ($row, $index) use ($grandTotal, $palette) {
            $total = (float) $row->total;
            $persen = $grandTotal > 0 ? round(($total / $grandTotal) * 100) : 0;

            return [
                'nama' => $row->kategori,
                'nominal' => 'Rp ' . number_format($total, 0, ',', '.'),
                'persen' => $persen,
                'warna' => $palette[$index % count($palette)],
            ];
        });

        return [
            'items' => $items,
            'total' => 'Rp ' . number_format($grandTotal, 0, ',', '.'),
            'periode' => $periode,
        ];
    }
}