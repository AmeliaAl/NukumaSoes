<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Asets\AsetResource;
use App\Models\Aset;
use Filament\Widgets\Widget;

class PerolehanTerbaruTable extends Widget
{
    protected string $view = 'filament.admin.widgets.perolehan-terbaru-table';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    protected function getViewData(): array
    {
        return [
            'asets' => Aset::query()
                ->latest('tanggal_perolehan')
                ->limit(5)
                ->get(),

            'urlSemuaAset' => AsetResource::getUrl('index'),
        ];
    }
}