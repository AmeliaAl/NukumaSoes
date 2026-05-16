<?php

namespace App\Filament\Admin\Resources\AsetLancars\Pages;

use App\Filament\Admin\Resources\AsetLancars\AsetLancarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsetLancar extends EditRecord
{
    protected static string $resource = AsetLancarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
