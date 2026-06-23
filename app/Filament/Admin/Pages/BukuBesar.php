<?php

namespace App\Filament\Admin\Pages;

use App\Models\coa as Coa;
use App\Models\JurnalDetail;
use App\Models\SaldoAwal;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        // Kumpulkan akun_id dari jurnal dalam rentang tanggal (exclude jurnal saldo awal)
        $akunIdsJurnal = JurnalDetail::query()
            ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id')
            ->where(fn ($q) => $q->whereNull('jurnal_umum.ref_type')
                ->orWhere('jurnal_umum.ref_type', '!=', 'saldo_awal'))
            ->when($this->dari, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '<=', $this->sampai))
            ->when($this->filterAkunId, fn ($q) => $q->where('jurnal_detail.akun_id', $this->filterAkunId))
            ->distinct()
            ->pluck('jurnal_detail.akun_id');

        // Tambahkan akun yang punya saldo awal dalam rentang ini
        $akunIdsSaldoAwal = collect();
        if ($this->dari) {
            $dari = \Carbon\Carbon::parse($this->dari);
            $akunIdsSaldoAwal = \App\Models\SaldoAwal::query()
                ->when($this->filterAkunId, fn ($q) => $q->where('coa_id', $this->filterAkunId))
                ->get()
                ->filter(function ($s) use ($dari) {
                    $tgl = \Carbon\Carbon::createFromDate($s->tahun, $s->bulan, 1);
                    return $tgl->lte($dari);
                })
                ->pluck('coa_id');
        }

        $akunIds = $akunIdsJurnal->merge($akunIdsSaldoAwal)->unique();

        $akuns = Coa::whereIn('id', $akunIds)
            ->orderBy('kode_akun')
            ->get();

        return $akuns->map(function ($akun) {
            // ── Hitung saldo awal sebelum rentang filter ─────────────────
            $saldoAwalBaris = 0.0;
            $saldoAwalRows  = collect();

            if ($this->dari) {
                $dari    = \Carbon\Carbon::parse($this->dari);
                $bulan   = (int) $dari->format('n');
                $tahun   = (int) $dari->format('Y');

                $efektif = \App\Models\SaldoAwal::getSaldoEfektif($akun->id, $bulan, $tahun);

                // Hitung semua mutasi jurnal SEBELUM tanggal filter (bukan saldo awal)
                // yang belum termasuk dalam getSaldoEfektif
                // getSaldoEfektif sudah include mutasi s/d hari sebelum bulan ini
                // Jadi tambahkan mutasi dari tgl 1 bulan ini s/d hari sebelum this->dari
                $tgl1Bulan = $dari->copy()->startOfMonth()->toDateString();
                if ($this->dari > $tgl1Bulan) {
                    $mutasiAwalBulan = \Illuminate\Support\Facades\DB::table('jurnal_detail')
                        ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id')
                        ->where('jurnal_detail.akun_id', $akun->id)
                        ->where(fn ($q) => $q->whereNull('jurnal_umum.ref_type')
                            ->orWhere('jurnal_umum.ref_type', '!=', 'saldo_awal'))
                        ->whereDate('jurnal_umum.tanggal', '>=', $tgl1Bulan)
                        ->whereDate('jurnal_umum.tanggal', '<', $this->dari)
                        ->selectRaw('SUM(debit) - SUM(kredit) as net')
                        ->value('net');
                    $efektif += (float) $mutasiAwalBulan;
                }

                if ($efektif != 0) {
                    $saldoAwalBaris = $efektif;
                    $saldoAwalRows = collect([[
                        'tanggal'    => $tgl1Bulan,
                        'keterangan' => 'Saldo Awal',
                        'ref'        => '-',
                        'debit'      => $efektif > 0 ? $efektif : 0,
                        'kredit'     => $efektif < 0 ? abs($efektif) : 0,
                        'saldo'      => $efektif,
                        'is_saldo_awal' => true,
                    ]]);
                }
            }

            // ── Ambil jurnal dalam rentang filter, exclude jurnal saldo awal ──
            $details = JurnalDetail::query()
                ->with('jurnal')
                ->where('akun_id', $akun->id)
                ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id')
                ->where(fn ($q) => $q->whereNull('jurnal_umum.ref_type')
                    ->orWhere('jurnal_umum.ref_type', '!=', 'saldo_awal'))
                ->when($this->dari, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '>=', $this->dari))
                ->when($this->sampai, fn ($q) => $q->whereDate('jurnal_umum.tanggal', '<=', $this->sampai))
                ->orderBy('jurnal_umum.tanggal')
                ->orderBy('jurnal_umum.id')
                ->select('jurnal_detail.*')
                ->get();

            $saldo = $saldoAwalBaris;
            $mutasiRows = $details->map(function ($detail) use (&$saldo) {
                $saldo += $detail->debit - $detail->kredit;
                return [
                    'tanggal'       => $detail->jurnal?->tanggal,
                    'keterangan'    => $detail->jurnal?->keterangan,
                    'ref'           => $detail->jurnal?->no_jurnal,
                    'debit'         => $detail->debit,
                    'kredit'        => $detail->kredit,
                    'saldo'         => $saldo,
                    'is_saldo_awal' => false,
                ];
            });

            $rows = $saldoAwalRows->concat($mutasiRows);

            return [
                'kode_akun'    => $akun->kode_akun,
                'nama_akun'    => $akun->nama_akun,
                'rows'         => $rows,
                'total_debit'  => $details->sum('debit') + ($saldoAwalBaris > 0 ? $saldoAwalBaris : 0),
                'total_kredit' => $details->sum('kredit') + ($saldoAwalBaris < 0 ? abs($saldoAwalBaris) : 0),
                'saldo_akhir'  => $saldo,
            ];
        });
    }
}
