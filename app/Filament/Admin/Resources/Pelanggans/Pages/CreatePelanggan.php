<?php

namespace App\Filament\Admin\Resources\Pelanggans\Pages;

use App\Filament\Admin\Resources\Pelanggans\PelangganResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePelanggan extends CreateRecord
{
    protected static string $resource = PelangganResource::class;
    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}

}
