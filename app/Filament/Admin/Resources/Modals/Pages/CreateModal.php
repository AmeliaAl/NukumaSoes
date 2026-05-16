<?php

namespace App\Filament\Admin\Resources\Modals\Pages;

use App\Filament\Admin\Resources\Modals\ModalResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\ModalService;

class CreateModal extends CreateRecord
{
    protected static string $resource = ModalResource::class;
    protected static ?string $title = 'Tambah Transaksi Modal';

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function afterCreate(): void
    {
        ModalService::buatJurnal($this->record);
    }
}
