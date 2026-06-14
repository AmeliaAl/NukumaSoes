<?php

namespace App\Filament\Admin\Resources\HargaBarangs\Pages;

use App\Filament\Admin\Resources\HargaBarangs\HargaBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHargaBarangs extends ListRecords
{
    protected static string $resource = HargaBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Harga'),
        ];
    }
}
