<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanKonsinyasis\PenjualanKonsinyasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePenjualanKonsinyasi extends CreateRecord
{
    protected static string $resource = PenjualanKonsinyasiResource::class;

    protected function getRedirectUrl(): string
    {
        return PenjualanKonsinyasiResource::getUrl('edit', [
            'record' => $this->record,
        ]);
    }
}
