<?php

namespace App\Filament\Admin\Resources\Pemeliharaans\Pages;

use App\Filament\Admin\Resources\Pemeliharaans\PemeliharaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeliharaans extends ListRecords
{
    protected static string $resource = PemeliharaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
