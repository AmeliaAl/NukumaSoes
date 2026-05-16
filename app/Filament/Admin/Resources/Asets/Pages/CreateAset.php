<?php

namespace App\Filament\Admin\Resources\Asets\Pages;

use App\Filament\Admin\Resources\Asets\AsetResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\AsetService;
use Illuminate\Database\Eloquent\Model;

class CreateAset extends CreateRecord
{
    protected static string $resource = AsetResource::class;
    protected static ?string $title = 'Tambah Aset Tetap';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nilai_buku'] = $data['nilai_perolehan'];
        $data['akumulasi_penyusutan'] = 0;

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return AsetService::beliCash($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
