<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\Pages;

use App\Filament\Admin\Resources\LaporanKonsinyasis\LaporanKonsinyasiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLaporanKonsinyasi extends EditRecord
{
    protected static string $resource = LaporanKonsinyasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    protected $listeners = [
        'updateTotalLaporan' => 'refreshTotal',
    ];
    public function refreshTotal()
    {
        $this->record->refresh();

        $data = $this->form->getState();
        $data['total_laporan'] = $this->record->detailLaporan()->sum('subtotal');

        $this->form->fill($data);
    }
}
