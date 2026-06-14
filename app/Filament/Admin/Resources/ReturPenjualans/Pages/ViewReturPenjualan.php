<?php

namespace App\Filament\Admin\Resources\ReturPenjualans\Pages;

use App\Filament\Admin\Resources\ReturPenjualans\ReturPenjualanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReturPenjualan extends ViewRecord
{
    protected static string $resource = ReturPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
