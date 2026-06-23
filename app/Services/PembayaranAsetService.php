<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;

class PembayaranAsetService
{
    public static function buatJurnal($pembayaran)
{
    $faktur = $pembayaran->faktur;

    return DB::transaction(function () use ($pembayaran, $faktur) {
        $akunHutang = Akun::where('no_akun', 211)->firstOrFail();

        // Cash → 110, Transfer → pakai id_akun pilihan user (111=BCA, 112=Mandiri)
        if ($pembayaran->metode_pembayaran === 'cash') {
            $akunKas = Akun::where('no_akun', 111)->firstOrFail();
        } else {
            $akunKas = Akun::findOrFail($pembayaran->id_akun);
        }

        $jurnal = Jurnal::create([
            'tanggal'      => $pembayaran->tanggal_bayar,
            'no_referensi' => $faktur->no_faktur,
            'deskripsi'    => 'Pembayaran aset faktur ' . $faktur->no_faktur,
        ]);

        $jurnal->details()->create([
            'no_akun'   => $akunHutang->id,
            'debit'     => $pembayaran->jumlah_bayar,
            'credit'    => 0,
            'deskripsi' => "Pelunasan hutang kepada {$faktur->vendor->nama_vendor}",
        ]);

        $jurnal->details()->create([
            'no_akun'   => $akunKas->id,
            'debit'     => 0,
            'credit'    => $pembayaran->jumlah_bayar,
            'deskripsi' => "Pembayaran via {$pembayaran->metode_pembayaran}",
        ]);

        return $jurnal;
    });
}
      // ✅ Helper: Get ID akun by kode
    protected static function getAkunId(string $kode): int
    {
        $akun = Akun::where('kode_akun', $kode)->first();
        
        if (!$akun) {
            throw new \Exception("Akun dengan kode {$kode} tidak ditemukan!");
        }
        
        return $akun->id;
    }

    // ✅ Helper: Update saldo akun
    protected static function updateSaldo(string $kode, float $debit, float $kredit): void
    {
        $akun = Akun::where('kode_akun', $kode)->first();
        
        if ($akun) {
            $akun->updateSaldo($debit, $kredit);
        }
    }
}