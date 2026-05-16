<?php

namespace App\Filament\Admin\Resources\LokasiAsets\Pages;

use App\Filament\Admin\Resources\LokasiAsets\LokasiAsetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLokasiAset extends EditRecord
{
    protected static string $resource = LokasiAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
