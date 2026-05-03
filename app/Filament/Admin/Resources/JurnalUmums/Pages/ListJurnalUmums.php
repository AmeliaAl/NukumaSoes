<?php

namespace App\Filament\Admin\Resources\JurnalUmums\Pages;

use App\Filament\Admin\Resources\JurnalUmums\JurnalUmumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJurnalUmums extends ListRecords
{
    protected static string $resource = JurnalUmumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Button "Buat" dihapus — jurnal dibuat otomatis oleh sistem
        ];
    }
}
