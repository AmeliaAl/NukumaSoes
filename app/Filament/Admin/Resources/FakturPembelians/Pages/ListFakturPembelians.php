<?php

namespace App\Filament\Admin\Resources\FakturPembelians\Pages;

use App\Filament\Admin\Resources\FakturPembelians\FakturPembelianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFakturPembelians extends ListRecords
{
    protected static string $resource = FakturPembelianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
