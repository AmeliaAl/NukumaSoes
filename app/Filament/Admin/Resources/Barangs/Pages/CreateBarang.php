<?php

namespace App\Filament\Admin\Resources\Barangs\Pages;

use App\Filament\Admin\Resources\Barangs\BarangResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;
    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}

}
