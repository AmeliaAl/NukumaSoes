<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\PenjualanNonKonsinyasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenjualanNonKonsinyasis extends ListRecords
{
    protected static string $resource = PenjualanNonKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
