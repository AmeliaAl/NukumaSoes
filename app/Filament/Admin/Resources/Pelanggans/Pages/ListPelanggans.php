<?php

namespace App\Filament\Admin\Resources\Pelanggans\Pages;

use App\Filament\Admin\Resources\Pelanggans\PelangganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPelanggans extends ListRecords
{
    protected static string $resource = PelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
