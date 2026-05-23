<?php

namespace App\Filament\Admin\Pages;

use App\Models\Coa;
use App\Models\JurnalDetail;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class BukuBesar extends Page
{
    protected string $view = 'filament.admin.pages.buku-besar';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Buku Besar';

    protected static ?string $title = 'Buku Besar';

    protected static \UnitEnum|string|null $navigationGroup = 'Akuntansi';

    protected static ?int $navigationSort = 2;

    public ?string $dari = null;
    public ?string $sampai = null;
    public ?string $inputDari = null;
    public ?string $inputSampai = null;
    public ?int $filterAkunId = null; // null = semua akun

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
        $this->filterAkunId = null;
    }

    public function getAkunOptions(): array
    {
        return Coa::orderBy('kode_akun')
            ->get()
            ->mapWithKeys(fn ($c) => [$c->id => $c->nama_akun])
            ->toArray();
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('dari')
                ->label('Tanggal Dari')
                ->placeholder('Pilih tanggal awal')
                ->live(),

            DatePicker::make('sampai')
                ->label('Tanggal Sampai')
                ->placeholder('Pilih tanggal akhir')
                ->live(),
        ])->columns(2);
    }

    public function getBukuBesarData(): Collection
    {
        $akunIds = JurnalDetail::query()
            ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id')
            ->when($this->dari, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '<=', $this->sampai))
            ->when($this->filterAkunId, fn ($q) => $q->where('jurnal_detail.akun_id', $this->filterAkunId))
            ->distinct()
            ->pluck('jurnal_detail.akun_id');

        $akuns = Coa::whereIn('id', $akunIds)
            ->orderBy('kode_akun')
            ->get();

        return $akuns->map(function ($akun) {
            $details = JurnalDetail::query()
                ->with('jurnal')
                ->where('akun_id', $akun->id)
                ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id')
                ->when($this->dari, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '>=', $this->dari))
                ->when($this->sampai, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '<=', $this->sampai))
                ->orderBy('jurnal_umum.tanggal')
                ->orderBy('jurnal_umum.id')
                ->select('jurnal_detail.*')
                ->get();

            $saldo = 0;
            $rows = $details->map(function ($detail) use (&$saldo) {
                $saldo += $detail->debit - $detail->kredit;
                return [
                    'tanggal'    => $detail->jurnal?->tanggal,
                    'keterangan' => $detail->jurnal?->keterangan,
                    'ref'        => $detail->jurnal?->no_jurnal,
                    'debit'      => $detail->debit,
                    'kredit'     => $detail->kredit,
                    'saldo'      => $saldo,
                ];
            });

            return [
                'kode_akun'    => $akun->kode_akun,
                'nama_akun'    => $akun->nama_akun,
                'rows'         => $rows,
                'total_debit'  => $details->sum('debit'),
                'total_kredit' => $details->sum('kredit'),
                'saldo_akhir'  => $saldo,
            ];
        });
    }
}
