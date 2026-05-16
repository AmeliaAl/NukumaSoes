<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Pages;

use App\Filament\Admin\Resources\SaldoAwals\SaldoAwalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\SaldoAwal;

class ListSaldoAwals extends ListRecords
{
    protected static string $resource = SaldoAwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Saldo Awal')
                ->icon('heroicon-o-plus'),
        ];
    }
}
