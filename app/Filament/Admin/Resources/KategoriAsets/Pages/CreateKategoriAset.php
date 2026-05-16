<?php

namespace App\Filament\Admin\Resources\KategoriAsets\Pages;

use App\Filament\Admin\Resources\KategoriAsets\KategoriAsetResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\KategoriAset;


class CreateKategoriAset extends CreateRecord
{
    protected static string $resource = KategoriAsetResource::class;
    protected static ?string $title = 'Tambah Kategori Aset';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (\App\Models\KategoriAset::where('kode_kategori', $data['kode_kategori'])->exists()) {
            Notification::make()
                ->warning()
                ->title("Kode {$data['kode_kategori']} sudah ada")
                ->send();

            // Throw exception atau redirect, jangan unset
            $this->halt(); // Livewire / Filament method untuk stop insert
        }

        return $data;
    }

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }



}
