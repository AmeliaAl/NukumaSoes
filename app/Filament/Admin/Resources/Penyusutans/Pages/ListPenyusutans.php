<?php

namespace App\Filament\Admin\Resources\Penyusutans\Pages;

use App\Filament\Admin\Resources\Penyusutans\PenyusutanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenyusutans extends ListRecords
{
    protected static string $resource = PenyusutanResource::class;

   /* protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }*/

    protected function getTableFiltersLayout(): FiltersLayout
{
    return FiltersLayout::AboveContent;
}

    protected function getTableHeader(): ?\Illuminate\Contracts\View\View
    {
        // Ambil filter state dengan benar
        $asetFilter = $this->tableFilters['aset_id'] ?? null;
        
        // Ekstrak ID dari filter
        $asetId = null;
        if (is_array($asetFilter)) {
            $asetId = $asetFilter['value'] ?? $asetFilter[0] ?? null;
        } else {
            $asetId = $asetFilter;
        }

        // Jika tidak ada filter, return null
        if (! $asetId) {
            return null;
        }

        // Ambil data aset
        $aset = \App\Models\Aset::with('kategori_aset')->find($asetId);

        // Jika aset tidak ditemukan, return null
        if (! $aset) {
            return null;
        }

        // Tampilkan view
        return view('filament.header.kartu-penyusutan', compact('aset'));
    }
}
