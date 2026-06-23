<?php

namespace App\Filament\Admin\Resources\BukuBesars\Pages;

use App\Filament\Admin\Resources\BukuBesars\BukuBesarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBukuBesar extends EditRecord
{
    protected static string $resource = BukuBesarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
