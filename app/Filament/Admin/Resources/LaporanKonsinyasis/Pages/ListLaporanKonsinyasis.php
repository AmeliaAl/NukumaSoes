<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\Pages;

use App\Filament\Admin\Resources\LaporanKonsinyasis\LaporanKonsinyasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLaporanKonsinyasis extends ListRecords
{
    protected static string $resource = LaporanKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
