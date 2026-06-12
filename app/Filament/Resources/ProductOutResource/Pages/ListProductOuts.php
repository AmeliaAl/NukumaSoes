<?php

namespace App\Filament\Resources\ProductOutResource\Pages;

use App\Filament\Resources\ProductOutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductOuts extends ListRecords
{
    protected static string $resource = ProductOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
