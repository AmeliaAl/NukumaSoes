<?php

namespace App\Filament\Admin\Resources\AsetLancars\Pages;

use App\Filament\Admin\Resources\AsetLancars\AsetLancarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsetLancars extends ListRecords
{
    protected static string $resource = AsetLancarResource::class;

    /*protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }*/
}
