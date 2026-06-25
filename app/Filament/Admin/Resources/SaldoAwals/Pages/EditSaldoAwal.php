<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Pages;

use App\Filament\Admin\Resources\SaldoAwals\SaldoAwalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Services\SaldoAwalService;

class EditSaldoAwal extends EditRecord
{
    protected static string $resource = SaldoAwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(function ($record) {
                    // Hapus jurnal setelah saldo awal dihapus
                    SaldoAwalService::hapusJurnal($record);
                }),
        ];
    }

    protected function afterSave(): void
    {
        // Update jurnal setelah saldo awal diupdate
        SaldoAwalService::buatJurnal($this->record);
    }
}
