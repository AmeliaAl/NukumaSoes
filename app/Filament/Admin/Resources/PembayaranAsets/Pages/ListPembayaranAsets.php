<?php

namespace App\Filament\Admin\Resources\PembayaranAsets\Pages;

use App\Filament\Admin\Resources\PembayaranAsets\PembayaranAsetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPembayaranAsets extends ListRecords
{
    protected static string $resource = PembayaranAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
