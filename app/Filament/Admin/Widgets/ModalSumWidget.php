<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use App\Models\Modal;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ModalSumWidget extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'filament.admin.widgets.modal-sum-widget';
    
    // TAMBAHKAN INI - biar widget reactive
    protected int | string | array $columnSpan = 'full';

        // TAMBAHKAN INI - polling setiap 3 detik (atau bisa dihapus kalau ga mau auto refresh)
    protected static ?string $pollingInterval = null;

    protected function getViewData(): array
    {
        $filters = $this->filters ?? [];

        $query = Modal::query();

        // Cek filter tanggal
        if (!empty($filters['tanggal']['from'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal']['from']);
        }

        if (!empty($filters['tanggal']['until'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal']['until']);
        }

        $setoran = (clone $query)->where('jenis', 'setoran')->sum('jumlah');
        $prive   = (clone $query)->where('jenis', 'prive')->sum('jumlah');

        return [
            'setoran' => $setoran,
            'prive' => $prive,
            'saldo' => $setoran - $prive,
        ];
    }
}