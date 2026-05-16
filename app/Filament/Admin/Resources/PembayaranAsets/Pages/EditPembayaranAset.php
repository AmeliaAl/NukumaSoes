<?php

namespace App\Filament\Admin\Resources\PembayaranAsets\Pages;

use App\Filament\Admin\Resources\PembayaranAsets\PembayaranAsetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPembayaranAset extends EditRecord
{
    protected static string $resource = PembayaranAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
