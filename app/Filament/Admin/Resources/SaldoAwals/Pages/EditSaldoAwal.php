<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Pages;

use App\Filament\Admin\Resources\SaldoAwals\SaldoAwalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSaldoAwal extends EditRecord
{
    protected static string $resource = SaldoAwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        \App\Services\JurnalPerpetualService::saldoAwal($this->record);
    }
}
