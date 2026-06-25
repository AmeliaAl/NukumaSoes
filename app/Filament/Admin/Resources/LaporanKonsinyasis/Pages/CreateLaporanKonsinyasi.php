<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\Pages;

use App\Filament\Admin\Resources\LaporanKonsinyasis\LaporanKonsinyasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanKonsinyasi extends CreateRecord
{
    protected static string $resource = LaporanKonsinyasiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total_laporan'] = $data['total_laporan'] ?? 0;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', [
            'record' => $this->getRecord(),
        ]);
    }
}