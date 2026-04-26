<?php

namespace App\Filament\Admin\Resources\Kategoris\Pages;

use App\Filament\Admin\Resources\Kategoris\KategoriResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditKategori extends EditRecord
{
    protected static string $resource = KategoriResource::class;

    public function getTitle(): string
    {
        return 'Edit Kategori';
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Simpan perubahan'),
            $this->getCancelFormAction()->label('Batal'),
        ];
    }
}