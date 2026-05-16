<?php

namespace App\Filament\Admin\Resources\PemakaianPersediaans\Pages;

use App\Filament\Admin\Resources\PemakaianPersediaans\PemakaianPersediaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemakaianPersediaans extends ListRecords
{
    protected static string $resource = PemakaianPersediaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
