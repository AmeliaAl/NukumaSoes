<?php

namespace App\Filament\Admin\Resources\HargaBarangs\Pages;

use App\Filament\Admin\Resources\HargaBarangs\HargaBarangResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHargaBarang extends CreateRecord
{
    protected static string $resource = HargaBarangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
