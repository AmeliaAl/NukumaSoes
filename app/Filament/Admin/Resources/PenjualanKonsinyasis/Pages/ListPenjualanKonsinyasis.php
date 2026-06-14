<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanKonsinyasis\PenjualanKonsinyasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenjualanKonsinyasis extends ListRecords
{
    protected static string $resource = PenjualanKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
