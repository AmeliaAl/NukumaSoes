<?php

namespace App\Filament\Admin\Resources\BukuBesars\Pages;

use App\Filament\Admin\Resources\BukuBesars\BukuBesarResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\BukuBesars\Widgets\BukuBesarTableOverview;

class ListBukuBesars extends ListRecords
{
    protected static string $resource = BukuBesarResource::class;



     protected function getHeaderWidgets(): array
    {
        return [
            BukuBesarTableOverview::class,
        ];
    }
}
