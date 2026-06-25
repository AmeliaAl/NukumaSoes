<?php

namespace App\Filament\Admin\Resources\DetailPersediaanProduks\Pages;

use App\Filament\Admin\Resources\DetailPersediaanProduks\DetailPersediaanProdukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDetailPersediaanProduks extends ListRecords
{
    protected static string $resource = DetailPersediaanProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
