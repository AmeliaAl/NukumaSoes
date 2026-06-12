<?php

namespace App\Filament\Resources\ProductOutResource\Pages;

use App\Filament\Resources\ProductOutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductOut extends EditRecord
{
    protected static string $resource = ProductOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
