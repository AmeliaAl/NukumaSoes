<?php

namespace App\Filament\Admin\Resources\ReturPenjualans\Pages;

use App\Filament\Admin\Resources\ReturPenjualans\ReturPenjualanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReturPenjualans extends ListRecords
{
    protected static string $resource = ReturPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
