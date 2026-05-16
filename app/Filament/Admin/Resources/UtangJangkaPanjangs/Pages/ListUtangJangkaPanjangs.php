<?php

namespace App\Filament\Admin\Resources\UtangJangkaPanjangs\Pages;

use App\Filament\Admin\Resources\UtangJangkaPanjangs\UtangJangkaPanjangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUtangJangkaPanjangs extends ListRecords
{
    protected static string $resource = UtangJangkaPanjangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Utang Jangka Panjang')
                ->icon('heroicon-o-plus'),
        ];
    }
}
