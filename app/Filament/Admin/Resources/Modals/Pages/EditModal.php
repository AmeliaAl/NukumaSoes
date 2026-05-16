<?php

namespace App\Filament\Admin\Resources\Modals\Pages;

use App\Filament\Admin\Resources\Modals\ModalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditModal extends EditRecord
{
    protected static string $resource = ModalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
