<?php

namespace App\Filament\Admin\Resources\BukuBesars\Widgets;

use Filament\Widgets\Widget;
use App\Models\Akun;
use App\Models\Jurnal;
use Carbon\Carbon;

class BukuBesarTableOverview extends Widget
{
    protected string $view = 'filament.admin.resources.buku-besars.widgets.buku-besar-table-overview';
    protected int | string | array $columnSpan = 'full';

    public $periode_awal;
    public $periode_akhir;
    public $id_akun; // no_akun yang dipilih (misal: 113, 111)
    public $jurnals = [];
    public $saldoAwal = 0;
    public $saldoAkhir = 0;
    public $posisiSaldo = null; // 'debit' atau 'credit'

    public function mount(): void
    {
        $this->periode_awal = request('periode_awal', now()->format('Y-m'));
        $this->periode_akhir = request('periode_akhir', now()->format('Y-m'));
        $this->id_akun = request('id_akun');
        
        $this->loadData();
    }

    public function filterJurnal(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->saldoAwal = 0;
        
        // ✅ Cari akun berdasarkan no_akun
        $akun = $this->id_akun ? Akun::where('no_akun', $this->id_akun)->first() : null;
        
        if (!$akun) {
            $this->jurnals = collect();
            $this->saldoAkhir = 0;
            $this->posisiSaldo = null;
            return;
        }

        $idAkun = $akun->id; // ID dari tabel akun
        
        // Tentukan saldo normal berdasarkan header_akun
        // Header 1 (Aset), 5 (Beban), 6, 7 → Saldo Normal DEBIT
        // Header 2 (Kewajiban), 3 (Ekuitas), 4 (Pendapatan) → Saldo Normal KREDIT
        $this->posisiSaldo = in_array($akun->header_akun, [1, 5, 6, 7]) ? 'debit' : 'credit';
        
        $jurnalsQuery = Jurnal::with(['jurnaldetail.akun'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if ($this->periode_awal && $this->periode_akhir) {
            $awal = Carbon::createFromFormat('Y-m', $this->periode_awal)->startOfMonth();
            $akhir = Carbon::createFromFormat('Y-m', $this->periode_akhir)->endOfMonth();

            // ✅ Hitung saldo awal (transaksi SEBELUM periode awal)
            $transaksiSebelum = Jurnal::where('tanggal', '<', $awal)
                ->whereHas('jurnaldetail', function ($q) use ($idAkun) {
                    $q->where('no_akun', $idAkun);
                })
                ->with(['jurnaldetail' => function ($query) use ($idAkun) {
                    $query->where('no_akun', $idAkun);
                }])
                ->get();

            $totalDebitAwal = $transaksiSebelum->flatMap->jurnaldetail->sum('debit');
            $totalKreditAwal = $transaksiSebelum->flatMap->jurnaldetail->sum('credit');

            // saldo awal dari transaksi sebelumnya
            if ($this->posisiSaldo === 'debit') {
                $this->saldoAwal = $totalDebitAwal - $totalKreditAwal;
            } else {
                $this->saldoAwal = $totalKreditAwal - $totalDebitAwal;
            }

            // 🔥 kalau belum ada histori transaksi, ambil dari tabel saldoawal
            if ((float) $this->saldoAwal == 0) {
                $saldoAwalManual = \App\Models\SaldoAwal::where('akun_id', $idAkun)
                    ->where('bulan', (int) $awal->month)
                    ->where('tahun', (int) $awal->year)
                    ->value('nominal');

                $this->saldoAwal = $saldoAwalManual ?? 0;
            }

            $jurnalsQuery->whereBetween('tanggal', [$awal, $akhir]);
        }

        // ✅ Filter jurnal yang punya transaksi di akun ini
        $jurnalsQuery->whereHas('jurnaldetail', function ($q) use ($idAkun) {
            $q->where('no_akun', $idAkun);
        });

        $this->jurnals = $jurnalsQuery->get();

        // ============================
        // 🔥 HITUNG SALDO AKHIR
        // ============================
        $totalDebit  = $this->jurnals->flatMap->jurnaldetail
            ->where('no_akun', $idAkun)
            ->sum('debit');
        $totalKredit = $this->jurnals->flatMap->jurnaldetail
            ->where('no_akun', $idAkun)
            ->sum('credit');

        if ($this->posisiSaldo === 'debit') {
            // Saldo Normal DEBIT
            $this->saldoAkhir  = $this->saldoAwal + $totalDebit - $totalKredit;
        } else {
            // Saldo Normal KREDIT
            $this->saldoAkhir  = $this->saldoAwal + $totalKredit - $totalDebit;
        }
    }
}