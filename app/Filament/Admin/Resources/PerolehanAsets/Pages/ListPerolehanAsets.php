<?php

namespace App\Filament\Admin\Resources\PerolehanAsets\Pages;

use App\Filament\Admin\Resources\PerolehanAsets\PerolehanAsetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPerolehanAsets extends ListRecords
{
    protected static string $resource = PerolehanAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
