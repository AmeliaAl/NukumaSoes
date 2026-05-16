<?php

namespace App\Filament\Admin\Resources\KategoriAsets\Pages;

use App\Filament\Admin\Resources\KategoriAsets\KategoriAsetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKategoriAset extends EditRecord
{
    protected static string $resource = KategoriAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
