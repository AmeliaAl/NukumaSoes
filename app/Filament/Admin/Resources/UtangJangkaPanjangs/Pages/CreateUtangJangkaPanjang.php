<?php

namespace App\Filament\Admin\Resources\UtangJangkaPanjangs\Pages;

use App\Filament\Admin\Resources\UtangJangkaPanjangs\UtangJangkaPanjangResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUtangJangkaPanjang extends CreateRecord
{
    protected static string $resource = UtangJangkaPanjangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Utang Jangka Panjang Berhasil Ditambahkan')
            ->body('Jurnal otomatis telah dibuat.');
    }
}
