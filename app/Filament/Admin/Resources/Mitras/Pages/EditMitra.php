<?php

namespace App\Filament\Admin\Resources\Mitras\Pages;

use App\Filament\Admin\Resources\Mitras\MitraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMitra extends EditRecord
{
    protected static string $resource = MitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
