<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Aset;
use App\Models\Penyusutan;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Widget;

class DetailPenyusutanAset extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.widgets.detail-penyusutan-aset';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public ?int $asetId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('asetId')
                ->label('Detail Penyusutan Aset')
                ->options(
                    Aset::orderBy('nama_aset')->pluck('nama_aset', 'id')
                )
                ->searchable()
                ->live(),
        ];
    }

    protected function getViewData(): array
{
    $aset = $this->asetId ? Aset::find($this->asetId) : null;

    $penyusutanTerakhir = null;
    $akumulasi = 0;
    $nilaiBuku = 0;
    $persen = 0;
    $status = 'Pilih aset dulu';

    if ($aset) {
        $penyusutanTerakhir = Penyusutan::where('aset_id', $aset->id)
            ->latest('periode')
            ->first();

        $akumulasi = $penyusutanTerakhir?->akumulasi_penyusutan ?? 0;
        $nilaiBuku = $penyusutanTerakhir?->nilai_buku ?? $aset->nilai_perolehan;

        $persen = $aset->nilai_perolehan > 0
            ? min(100, round(($akumulasi / $aset->nilai_perolehan) * 100, 1))
            : 0;

        if ($persen < 50) {
            $status = 'Kondisi aset masih bagus';
        } elseif ($persen < 80) {
            $status = 'Aset mulai menurun';
        } else {
            $status = 'Aset hampir habis umur manfaat';
        }
    }

    return [
        'aset' => $aset,
        'penyusutanTerakhir' => $penyusutanTerakhir,
        'akumulasi' => $akumulasi,
        'nilaiBuku' => $nilaiBuku,
        'persen' => $persen,
        'status' => $status,
    ];
}
}