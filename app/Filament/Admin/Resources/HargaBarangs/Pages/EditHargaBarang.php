<?php

namespace App\Filament\Admin\Resources\HargaBarangs\Pages;

use App\Filament\Admin\Resources\HargaBarangs\HargaBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHargaBarang extends EditRecord
{
    protected static string $resource = HargaBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
