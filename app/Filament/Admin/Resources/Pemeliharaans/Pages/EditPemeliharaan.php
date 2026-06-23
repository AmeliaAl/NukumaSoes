<?php

namespace App\Filament\Admin\Resources\Pemeliharaans\Pages;

use App\Filament\Admin\Resources\Pemeliharaans\PemeliharaanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemeliharaan extends EditRecord
{
    protected static string $resource = PemeliharaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
