<?php

namespace App\Filament\Admin\Resources\DetailPersediaanProduks\Pages;

use App\Filament\Admin\Resources\DetailPersediaanProduks\DetailPersediaanProdukResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDetailPersediaanProduk extends CreateRecord
{
    protected static string $resource = DetailPersediaanProdukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
