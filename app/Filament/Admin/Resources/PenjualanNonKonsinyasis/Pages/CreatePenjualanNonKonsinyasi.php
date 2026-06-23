<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\PenjualanNonKonsinyasiResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\JurnalPerpetualService;

class CreatePenjualanNonKonsinyasi extends CreateRecord
{
    protected static string $resource = PenjualanNonKonsinyasiResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values untuk field yang required di database
        $data['total'] = $data['total'] ?? 0;
        $data['total_hpp'] = $data['total_hpp'] ?? 0;
        $data['total_terbayar'] = $data['total_terbayar'] ?? 0;
        $data['diskon'] = $data['diskon'] ?? 0;
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        JurnalPerpetualService::penjualanNonKonsinyasi($this->record);
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', [
            'record' => $this->record,
        ]);
    }

}
