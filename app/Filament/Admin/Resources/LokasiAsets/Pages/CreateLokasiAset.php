<?php

namespace App\Filament\Admin\Resources\LokasiAsets\Pages;

use App\Filament\Admin\Resources\LokasiAsets\LokasiAsetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLokasiAset extends CreateRecord
{
    protected static string $resource = LokasiAsetResource::class;
    protected static ?string $title = 'Tambah Lokasi Aset';

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}


