<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanKonsinyasis\PenjualanKonsinyasiResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPenjualanKonsinyasi extends ViewRecord
{
    protected static string $resource = PenjualanKonsinyasiResource::class;
    // ViewPenjualanKonsinyasi.php
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }

}
