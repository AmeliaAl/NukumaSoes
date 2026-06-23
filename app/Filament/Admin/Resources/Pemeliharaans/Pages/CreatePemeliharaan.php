<?php

namespace App\Filament\Admin\Resources\Pemeliharaans\Pages;

use App\Filament\Admin\Resources\Pemeliharaans\PemeliharaanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\PemeliharaanService;

class CreatePemeliharaan extends CreateRecord
{
    protected static string $resource = PemeliharaanResource::class;
    protected static ?string $title = 'Tambah Transaksi Pemeliharaan';

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function afterCreate(): void
    {
        PemeliharaanService::buatJurnal($this->record);
    }
}
