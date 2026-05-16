<?php

namespace App\Filament\Admin\Resources\Vendors\Pages;

use App\Filament\Admin\Resources\Vendors\VendorResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\Vendor;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;
    protected static ?string $title = 'Tambah Data Vendor';

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (\App\Models\Vendor::where('nama_vendor', $data['nama_vendor'])->exists()) {
            Notification::make()
                ->warning()
                ->title("Nama {$data['nama_vendor']} sudah terdaftar.")
                ->send();

            // Throw exception atau redirect, jangan unset
            $this->halt(); // Livewire / Filament method untuk stop insert
        }

        return $data;
    }
}
