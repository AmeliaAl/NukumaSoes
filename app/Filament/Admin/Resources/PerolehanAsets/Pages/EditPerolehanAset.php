<?php

namespace App\Filament\Admin\Resources\PerolehanAsets\Pages;

use App\Filament\Admin\Resources\PerolehanAsets\PerolehanAsetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPerolehanAset extends EditRecord
{
    protected static string $resource = PerolehanAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
