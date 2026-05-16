<?php

namespace App\Filament\Admin\Resources\UtangJangkaPanjangs\Pages;

use App\Filament\Admin\Resources\UtangJangkaPanjangs\UtangJangkaPanjangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUtangJangkaPanjang extends EditRecord
{
    protected static string $resource = UtangJangkaPanjangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Utang Jangka Panjang')
                ->modalDescription('Jurnal terkait akan ikut terhapus. Yakin ingin melanjutkan?'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Utang Jangka Panjang Berhasil Diperbarui')
            ->body('Data telah disimpan.');
    }
}
