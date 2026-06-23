<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aset;
use App\Models\Penyusutan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProsesDepresiasiAset extends Command
{
    protected $signature = 'aset:depresiasi 
                            {--bulan= : Bulan yang akan diproses (format: YYYY-MM)}
                            {--force : Paksa jalankan meski sudah ada}
                            {--dry-run : Simulasi tanpa menyimpan ke database}';
    
    protected $description = 'Proses depresiasi/penyusutan aset tetap untuk bulan tertentu';

    public function handle()
{
    $asets = Aset::all();

    foreach ($asets as $aset) {

        $start = Carbon::parse($aset->tanggal_perolehan);
        $masaManfaat = $aset->masa_manfaat * 12;

        for ($i = 0; $i < $masaManfaat; $i++) {

            $periode = $start->copy()->addMonths($i)->format('Y-m');

            $sudahAda = Penyusutan::where('aset_id', $aset->id)
                ->where('periode', $periode)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            $this->prosesPenyusutan($aset, $periode);
        }
    }

    $this->info("Kartu penyusutan berhasil dibuat.");

    return Command::SUCCESS;
}
    protected function prosesPenyusutan(Aset $aset, string $periode)
    {
        $bebanPenyusutan = $this->hitungBebanPenyusutan($aset);
        $akumulasiPenyusutanBaru = $aset->akumulasi_penyusutan + $bebanPenyusutan;
        $nilaiBukuBaru = $aset->nilai_perolehan - $akumulasiPenyusutanBaru;

        if ($nilaiBukuBaru < $aset->nilai_residu) {
            $bebanPenyusutan = $aset->nilai_buku - $aset->nilai_residu;
            $akumulasiPenyusutanBaru = $aset->nilai_perolehan - $aset->nilai_residu;
            $nilaiBukuBaru = $aset->nilai_residu;
        }

        Penyusutan::create([
            'aset_id' => $aset->id,
            'periode' => $periode,
            'beban_penyusutan' => $bebanPenyusutan,
            'akumulasi_penyusutan' => $akumulasiPenyusutanBaru,
            'nilai_buku' => $nilaiBukuBaru,
        ]);

        $aset->update([
            'akumulasi_penyusutan' => $akumulasiPenyusutanBaru,
            'nilai_buku' => $nilaiBukuBaru,
            'status' => $nilaiBukuBaru <= $aset->nilai_residu ? 'habis_disusutkan' : 'aktif',
        ]);
    }

    protected function hitungBebanPenyusutan(Aset $aset): float
    {
        return match($aset->metode_penyusutan) {
            'garis_lurus' => $this->hitungGarisLurus($aset),
            'saldo_menurun' => $this->hitungSaldoMenurun($aset),
            'jumlah_angka_tahun' => $this->hitungJumlahAngkaTahun($aset),
            default => 0,
        };
    }

    protected function hitungGarisLurus(Aset $aset): float
    {
        $nilaiSusut = $aset->nilai_perolehan - $aset->nilai_residu;
        $masaManfaatBulan = $aset->masa_manfaat * 12;
        return $masaManfaatBulan > 0 ? $nilaiSusut / $masaManfaatBulan : 0;
    }

    protected function hitungSaldoMenurun(Aset $aset): float
    {
        $rate = (2 / $aset->masa_manfaat) / 12;
        return $aset->nilai_buku * $rate;
    }

    protected function hitungJumlahAngkaTahun(Aset $aset): float
    {
        $n = $aset->masa_manfaat;
        $jumlahAngkaTahun = ($n * ($n + 1)) / 2;
        $nilaiSusutPerTahun = ($aset->nilai_perolehan - $aset->nilai_residu) / $jumlahAngkaTahun;
        $tahunTersisa = $n - floor(($aset->akumulasi_penyusutan / $nilaiSusutPerTahun));
        $tahunTersisa = max(1, $tahunTersisa);
        return ($tahunTersisa * $nilaiSusutPerTahun) / 12;
    }
    
    
}