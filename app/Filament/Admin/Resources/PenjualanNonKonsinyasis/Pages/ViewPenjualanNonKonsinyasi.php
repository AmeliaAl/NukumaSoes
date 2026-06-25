<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\PenjualanNonKonsinyasiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPenjualanNonKonsinyasi extends ViewRecord
{
    protected static string $resource = PenjualanNonKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn ($record) => $record->status !== 'LUNAS'),
        ];
    }
}
