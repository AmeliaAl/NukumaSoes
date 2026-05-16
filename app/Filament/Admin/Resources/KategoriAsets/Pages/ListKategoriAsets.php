<?php

namespace App\Filament\Admin\Resources\KategoriAsets\Pages;

use App\Filament\Admin\Resources\KategoriAsets\KategoriAsetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKategoriAsets extends ListRecords
{
    protected static string $resource = KategoriAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
