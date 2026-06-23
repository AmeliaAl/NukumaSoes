<?php

namespace App\Filament\Admin\Resources\Jurnals\Pages;

use App\Filament\Admin\Resources\Jurnals\JurnalResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\Jurnals\Widgets\JurnalTableOverview;

class ListJurnals extends ListRecords
{
    protected static string $resource = JurnalResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            JurnalTableOverview::class,
        ];
    }

    // Sembunyikan tabel resource karena sudah pakai widget
    protected  string $view = 'filament.admin.resources.jurnals.pages.list-jurnals';
}
