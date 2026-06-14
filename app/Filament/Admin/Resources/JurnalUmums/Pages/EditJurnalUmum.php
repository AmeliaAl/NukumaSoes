<?php

namespace App\Filament\Admin\Resources\JurnalUmums\Pages;

use App\Filament\Admin\Resources\JurnalUmums\JurnalUmumResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditJurnalUmum extends EditRecord
{
    protected static string $resource = JurnalUmumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
