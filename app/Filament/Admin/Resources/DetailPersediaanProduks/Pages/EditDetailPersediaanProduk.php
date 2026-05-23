<?php

namespace App\Filament\Admin\Resources\DetailPersediaanProduks\Pages;

use App\Filament\Admin\Resources\DetailPersediaanProduks\DetailPersediaanProdukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDetailPersediaanProduk extends EditRecord
{
    protected static string $resource = DetailPersediaanProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
