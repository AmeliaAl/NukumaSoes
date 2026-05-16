<?php

namespace App\Filament\Admin\Resources\Jurnals\Pages;

use App\Filament\Admin\Resources\Jurnals\JurnalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJurnal extends EditRecord
{
    protected static string $resource = JurnalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
