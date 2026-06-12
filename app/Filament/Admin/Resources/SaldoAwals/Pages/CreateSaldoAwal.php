<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Pages;

use App\Filament\Admin\Resources\SaldoAwals\SaldoAwalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSaldoAwal extends CreateRecord
{
    protected static string $resource = SaldoAwalResource::class;

    protected function afterCreate(): void
    {
        \App\Services\JurnalPerpetualService::saldoAwal($this->record);
    }
}
