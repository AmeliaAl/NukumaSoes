<?php

namespace App\Filament\Admin\Resources\Persediaans\Pages;

use App\Filament\Admin\Resources\Persediaans\PersediaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPersediaans extends ListRecords
{
    protected static string $resource = PersediaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
