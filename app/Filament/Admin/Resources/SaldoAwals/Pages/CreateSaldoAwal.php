<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Pages;

use App\Filament\Admin\Resources\SaldoAwals\SaldoAwalResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\SaldoAwalService;

class CreateSaldoAwal extends CreateRecord
{
    protected static string $resource = SaldoAwalResource::class;

    protected function getRedirectUrl(): string
        {
            return $this->getResource()::getUrl('index');
        }

    protected function afterCreate(): void
        {
            SaldoAwalService::buatJurnal($this->record);
        }
}
