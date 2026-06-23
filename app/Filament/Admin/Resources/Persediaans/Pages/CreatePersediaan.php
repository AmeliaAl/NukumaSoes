<?php

namespace App\Filament\Admin\Resources\Persediaans\Pages;

use App\Filament\Admin\Resources\Persediaans\PersediaanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePersediaan extends CreateRecord
{
    protected static string $resource = PersediaanResource::class;

        public function getTitle(): string
        {
            return 'Bahan Habis Pakai'; // ubah di sini
        }

        protected function getRedirectUrl(): string
        {
            return $this->getResource()::getUrl('index');
        }

        protected function beforeCreate(): void
        {
            if (! $this->data['id_kategori']) {
                throw new \Exception('Kategori aset wajib.');
            }
        }


}
