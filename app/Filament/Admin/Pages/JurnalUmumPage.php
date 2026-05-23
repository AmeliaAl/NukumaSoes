<?php

namespace App\Filament\Admin\Pages;

use App\Models\JurnalDetail;
use Filament\Pages\Page;

class JurnalUmumPage extends Page
{
    protected string $view = 'filament.admin.pages.jurnal-umum';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Jurnal Umum';
    protected static ?string $title = 'Jurnal Umum';
    protected static \UnitEnum|string|null $navigationGroup = 'Akuntansi';
    protected static ?int $navigationSort = 1;

    public ?string $dari        = null;
    public ?string $sampai      = null;
    public ?string $inputDari   = null;
    public ?string $inputSampai = null;

    public function applyFilter(): void
    {
        $this->dari   = $this->inputDari;
        $this->sampai = $this->inputSampai;
    }

    public function resetFilter(): void
    {
        $this->dari        = null;
        $this->sampai      = null;
        $this->inputDari   = null;
        $this->inputSampai = null;
    }

    public function getData(): \Illuminate\Support\Collection
    {
        $details = \App\Models\JurnalDetail::query()
            ->with(['jurnal', 'akun'])
            ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id')
            ->when($this->dari,   fn ($q) => $q->whereDate('jurnal_umum.tanggal', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '<=', $this->sampai))
            ->orderBy('jurnal_umum.tanggal')
            ->orderBy('jurnal_umum.id')
            ->orderByRaw('jurnal_detail.debit DESC')
            ->select('jurnal_detail.*')
            ->get();

        // Group per jurnal_umum_id agar 1 jurnal = 1 tanggal
        return $details->groupBy('jurnal_umum_id')->map(function ($items) {
            return [
                'tanggal'    => optional($items->first()->jurnal)->tanggal,
                'keterangan' => optional($items->first()->jurnal)->keterangan,
                'no_jurnal'  => optional($items->first()->jurnal)->no_jurnal,
                'baris'      => $items,
            ];
        })->values();
    }
}
