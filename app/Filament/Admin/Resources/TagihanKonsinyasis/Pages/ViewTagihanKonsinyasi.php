<?php

namespace App\Filament\Admin\Resources\TagihanKonsinyasis\Pages;

use App\Filament\Admin\Resources\TagihanKonsinyasis\TagihanKonsinyasiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTagihanKonsinyasi extends ViewRecord
{
    protected static string $resource = TagihanKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
