<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\PenjualanNonKonsinyasiResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\JurnalPerpetualService;

class CreatePenjualanNonKonsinyasi extends CreateRecord
{
    protected static string $resource = PenjualanNonKonsinyasiResource::class;
    protected function afterCreate(): void
    {
        JurnalPerpetualService::penjualanNonKonsinyasi($this->record);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', [
            'record' => $this->record,
        ]);
    }

}
