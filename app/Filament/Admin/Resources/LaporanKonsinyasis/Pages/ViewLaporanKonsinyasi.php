<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\Pages;

use App\Filament\Admin\Resources\LaporanKonsinyasis\LaporanKonsinyasiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLaporanKonsinyasi extends ViewRecord
{
    protected static string $resource = LaporanKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
