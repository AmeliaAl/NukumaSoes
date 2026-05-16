<?php

namespace App\Filament\Admin\Resources\LokasiAsets\Pages;

use App\Filament\Admin\Resources\LokasiAsets\LokasiAsetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLokasiAsets extends ListRecords
{
    protected static string $resource = LokasiAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
