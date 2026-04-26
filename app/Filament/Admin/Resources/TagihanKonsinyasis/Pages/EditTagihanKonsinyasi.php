<?php

namespace App\Filament\Admin\Resources\TagihanKonsinyasis\Pages;

use App\Filament\Admin\Resources\TagihanKonsinyasis\TagihanKonsinyasiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTagihanKonsinyasi extends EditRecord
{
    protected static string $resource = TagihanKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
