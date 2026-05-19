<?php

namespace App\Filament\Admin\Resources\Penyusutans\Pages;

use App\Filament\Admin\Resources\Penyusutans\PenyusutanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Enums\FiltersLayout;
use App\Models\Aset;
use App\Models\Penyusutan;

class ListPenyusutans extends ListRecords
{
    protected static string $resource = PenyusutanResource::class;

    public $selectedAset = null;
    public $daftarAset = [];
    public $penyusutans = [];

    public function mount(): void
    {
        parent::mount();
        
        // Load daftar aset
        $this->daftarAset = Aset::orderBy('nama_aset')->get();
    }

    public function updatedSelectedAset($value)
    {
        if ($value) {
            $this->penyusutans = Penyusutan::with('aset.kategori_aset')
                ->where('aset_id', $value)
                ->orderBy('periode', 'asc')
                ->get();
        } else {
            $this->penyusutans = [];
        }
    }

    // Sembunyikan tabel resource karena pakai custom view
    public function getView(): string
    {
        return 'filament.admin.resources.penyusutans.pages.list-penyusutans';
    }
}
