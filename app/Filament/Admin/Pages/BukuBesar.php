<?php

namespace App\Filament\Admin\Pages;

use App\Models\Akun;
use App\Models\Jurnal;
use Carbon\Carbon;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use BackedEnum;

class BukuBesar extends Page
{
    protected  string $view = 'filament.admin.resources.buku-besars.widgets.buku-besar-table-overview';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $title = 'Buku Besar';
    protected static string|\UnitEnum|null $navigationGroup = 'Akuntansi';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 2;

    // Livewire properties (seperti di Widget)
    public ?string $periode_awal = null;
    public ?string $periode_akhir = null;
    public $id_akun = null;

    public $jurnals;
    public $saldoAwal = 0;
    public $posisiSaldo = 'debit';

    public function mount(): void
    {
        $now = Carbon::now();
        $this->periode_awal = $now->format('Y-m');
        $this->periode_akhir = $now->format('Y-m');
        
        $this->filterJurnal();
    }

    #[On('filterJurnal')]
    public function filterJurnal(): void
    {
        // Parse periode
        $periodeAwal = $this->periode_awal 
            ? Carbon::createFromFormat('Y-m', $this->periode_awal)->startOfMonth()
            : Carbon::now()->startOfMonth();
        
        $periodeAkhir = $this->periode_akhir
            ? Carbon::createFromFormat('Y-m', $this->periode_akhir)->endOfMonth()
            : Carbon::now()->endOfMonth();

        // Ambil jurnal dalam periode dan filter by akun jika dipilih
        $query = Jurnal::whereBetween('tanggal', [$periodeAwal, $periodeAkhir])
            ->where(function($q) {
                $q->where('no_referensi', 'not like', 'SALDO-%')
                  ->orWhereNull('no_referensi');
            })
            ->with('jurnaldetail.akun')
            ->orderBy('tanggal')
            ->orderBy('id');

        // Jika akun dipilih, filter jurnal yang memiliki detail untuk akun tersebut
        if ($this->id_akun) {
            $akunDipilih = Akun::where('no_akun', $this->id_akun)->first();
            
            if ($akunDipilih) {
                $query->whereHas('jurnaldetail', function ($q) use ($akunDipilih) {
                    $q->where('no_akun', $akunDipilih->id);
                });

                // Hitung saldo awal (sebelum periode) - TERMASUK SALDO-xxx
                $transaksiSebelum = Jurnal::where('tanggal', '<', $periodeAwal)
                    ->whereHas('jurnaldetail', function ($q) use ($akunDipilih) {
                        $q->where('no_akun', $akunDipilih->id);
                    })
                    ->with(['jurnaldetail' => function ($query) use ($akunDipilih) {
                        $query->where('no_akun', $akunDipilih->id);
                    }])
                    ->get();

                $debitAwal = $transaksiSebelum->flatMap->jurnaldetail->sum('debit');
                $kreditAwal = $transaksiSebelum->flatMap->jurnaldetail->sum('credit');

                // Deteksi saldo normal akun berdasarkan header_akun
                // Header 1,5,6,7 = Debit Normal | Header 2,3,4 = Kredit Normal
                $isDebitNormal = in_array($akunDipilih->header_akun, [1, 5, 6, 7]);

                if ($isDebitNormal) {
                    $this->saldoAwal = $debitAwal - $kreditAwal;
                    $this->posisiSaldo = 'debit';
                } else {
                    $this->saldoAwal = $kreditAwal - $debitAwal;
                    $this->posisiSaldo = 'kredit';
                }

                // Jika belum ada histori transaksi, ambil dari tabel saldoawal
                if ((float) $this->saldoAwal == 0) {
                    $saldoAwalManual = \App\Models\SaldoAwal::where('akun_id', $akunDipilih->id)
                        ->where('bulan', (int) $periodeAwal->month)
                        ->where('tahun', (int) $periodeAwal->year)
                        ->value('nominal');

                    $this->saldoAwal = $saldoAwalManual ?? 0;
                }
            }
        } else {
            $this->saldoAwal = 0;
            $this->posisiSaldo = 'debit';
        }

        $this->jurnals = $query->get();
    }
}
