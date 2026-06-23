<?php

namespace App\Filament\Admin\Resources\Asets\Pages;

use App\Filament\Admin\Resources\Asets\AsetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsets extends ListRecords
{
    protected static string $resource = AsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
