<?php

namespace App\Filament\Admin\Resources\TagihanKonsinyasis\Pages;

use App\Filament\Admin\Resources\TagihanKonsinyasis\TagihanKonsinyasiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTagihanKonsinyasi extends EditRecord
{
    protected static string $resource = TagihanKonsinyasiResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Redirect ke halaman View jika tagihan sudah LUNAS
        if ($this->record->status === 'LUNAS') {
            $this->redirect(TagihanKonsinyasiResource::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
