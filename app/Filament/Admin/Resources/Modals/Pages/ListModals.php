<?php

namespace App\Filament\Admin\Resources\Modals\Pages;

use App\Filament\Admin\Resources\Modals\ModalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListModals extends ListRecords
{
    protected static string $resource = ModalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /*protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\ModalSumWidget::class,
        ];
    }*/
}
