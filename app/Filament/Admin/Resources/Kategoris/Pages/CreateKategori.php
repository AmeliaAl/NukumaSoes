<?php

namespace App\Filament\Admin\Resources\Kategoris\Pages;

use App\Filament\Admin\Resources\Kategoris\KategoriResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateKategori extends CreateRecord
{
    protected static string $resource = KategoriResource::class;

    public function getTitle(): string
    {
        return 'Tambah Kategori';
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah';
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Tambah'),
            $this->getCreateAnotherFormAction()->label('Tambah & tambah lagi'),
            $this->getCancelFormAction()->label('Batal'),
        ];
    }
}