<?php

namespace App\Filament\Admin\Resources\Persediaans\Pages;

use App\Filament\Admin\Resources\Persediaans\PersediaanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPersediaan extends EditRecord
{
    protected static string $resource = PersediaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
