<?php

namespace App\Filament\Admin\Resources\TagihanKonsinyasis\Pages;

use App\Filament\Admin\Resources\TagihanKonsinyasis\TagihanKonsinyasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTagihanKonsinyasis extends ListRecords
{
    protected static string $resource = TagihanKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
