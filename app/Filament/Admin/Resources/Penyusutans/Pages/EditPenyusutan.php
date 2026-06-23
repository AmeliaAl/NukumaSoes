<?php

namespace App\Filament\Admin\Resources\Penyusutans\Pages;

use App\Filament\Admin\Resources\Penyusutans\PenyusutanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenyusutan extends EditRecord
{
    protected static string $resource = PenyusutanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
