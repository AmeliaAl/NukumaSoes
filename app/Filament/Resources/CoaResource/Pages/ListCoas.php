<?php

namespace App\Filament\Resources\CoaResource\Pages;

use App\Filament\Resources\CoaResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListCoas extends ListRecords
{
    protected static string $resource = CoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
