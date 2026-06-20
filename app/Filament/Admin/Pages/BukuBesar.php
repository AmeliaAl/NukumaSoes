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

            // Hitung saldo awal dari jurnal saldo awal (deskripsi mengandung 'saldo awal')
            $querySaldoAwal = JurnalDetail::query()
                ->where('no_akun', $akun->id)
                ->join('jurnal', 'jurnal_detail.id_jurnal', '=', 'jurnal.id')
                ->where('jurnal.deskripsi', 'like', '%saldo awal%');
            
            if ($this->dari) {
                $querySaldoAwal->where(function($q) {
                    $q->whereDate('jurnal.tanggal', '<', $this->dari)
                      ->orWhere(function($subQ) {
                          $subQ->whereDate('jurnal.tanggal', '=', $this->dari);
                      });
                });
            }

            $saldoAwalDebit = $querySaldoAwal->sum('jurnal_detail.debit');
            $saldoAwalKredit = $querySaldoAwal->sum('jurnal_detail.credit');
            
            $saldoAwal = $isDebitNormal 
                ? ($saldoAwalDebit - $saldoAwalKredit) 
                : ($saldoAwalKredit - $saldoAwalDebit);

            // Ambil transaksi KECUALI jurnal saldo awal
            $queryTransaksi = JurnalDetail::query()
                ->with('jurnal')
                ->where('no_akun', $akun->id)
                ->join('jurnal', 'jurnal_detail.id_jurnal', '=', 'jurnal.id')
                ->where('jurnal.deskripsi', 'not like', '%saldo awal%');
            
            if ($this->dari) {
                $queryTransaksi->whereDate('jurnal.tanggal', '>=', $this->dari);
            }
            if ($this->sampai) {
                $queryTransaksi->whereDate('jurnal.tanggal', '<=', $this->sampai);
            }
            
            $details = $queryTransaksi
                ->orderBy('jurnal.tanggal')
                ->orderBy('jurnal.id')
                ->select('jurnal_detail.*')
                ->get();

            $saldo = $saldoAwal;
            $rows = $details->map(function ($detail) use (&$saldo, $isDebitNormal) {
                if ($isDebitNormal) {
                    $saldo += $detail->debit - $detail->credit;
                } else {
                    $saldo += $detail->credit - $detail->debit;
                }
                
                return [
                    'tanggal'    => $detail->jurnal?->tanggal,
                    'keterangan' => $detail->jurnal?->deskripsi,
                    'ref'        => $detail->jurnal?->no_referensi,
                    'debit'      => $detail->debit,
                    'kredit'     => $detail->credit,
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
                'total_kredit'  => $details->sum('credit'),
                'saldo_akhir'   => $saldo,
            ];
        })->filter(function ($akun) {
            return count($akun['rows']) > 0 || $akun['saldo_awal'] != 0;
        })->values();
    }
}
