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
   protected static bool $shouldRegisterNavigation = false;

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
        $akunQuery = Coa::orderBy('kode_akun');
        if ($this->filterAkunId) {
            $akunQuery->where('id', $this->filterAkunId);
        }
        $akuns = $akunQuery->get();

        return $akuns->map(function ($akun) {
            $awalKode = substr((string)$akun->kode_akun, 0, 1);
            $isDebitNormal = in_array($awalKode, ['1', '5', '6', '8', '9']);

            $querySaldoAwal = JurnalDetail::query()
                ->where('akun_id', $akun->id)
                ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id');
            
            if ($this->dari) {
                $querySaldoAwal->where(function($q) {
                    $q->whereDate('jurnal_umum.tanggal', '<', $this->dari)
                      ->orWhere(function($subQ) {
                          $subQ->whereDate('jurnal_umum.tanggal', '=', $this->dari)
                               ->where('jurnal_umum.keterangan', 'like', 'Saldo awal %');
                      });
                });
            } else {
                $querySaldoAwal->where('jurnal_umum.keterangan', 'like', 'Saldo awal %');
            }

            $saldoAwalDebit = $querySaldoAwal->sum('jurnal_detail.debit');
            $saldoAwalKredit = $querySaldoAwal->sum('jurnal_detail.kredit');
            
            $saldoAwal = $isDebitNormal 
                ? ($saldoAwalDebit - $saldoAwalKredit) 
                : ($saldoAwalKredit - $saldoAwalDebit);

            $queryTransaksi = JurnalDetail::query()
                ->with('jurnal')
                ->where('akun_id', $akun->id)
                ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id')
                ->where('jurnal_umum.keterangan', 'not like', 'Saldo awal %');
            
            if ($this->dari) {
                $queryTransaksi->whereDate('jurnal_umum.tanggal', '>=', $this->dari);
            }
            if ($this->sampai) {
                $queryTransaksi->whereDate('jurnal_umum.tanggal', '<=', $this->sampai);
            }
            
            $details = $queryTransaksi
                ->orderBy('jurnal_umum.tanggal')
                ->orderBy('jurnal_umum.id')
                ->select('jurnal_detail.*')
                ->get();

            $saldo = $saldoAwal;
            $rows = $details->map(function ($detail) use (&$saldo, $isDebitNormal) {
                if ($isDebitNormal) {
                    $saldo += $detail->debit - $detail->kredit;
                } else {
                    $saldo += $detail->kredit - $detail->debit;
                }
                
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
                'kode_akun'     => $akun->kode_akun,
                'nama_akun'     => $akun->nama_akun,
                'is_debit_normal'=> $isDebitNormal,
                'saldo_awal'    => $saldoAwal,
                'rows'          => $rows,
                'total_debit'   => $details->sum('debit'),
                'total_kredit'  => $details->sum('kredit'),
                'saldo_akhir'   => $saldo,
            ];
        })->filter(function ($akun) {
            return count($akun['rows']) > 0 || $akun['saldo_awal'] != 0;
        })->values();
    }
}
