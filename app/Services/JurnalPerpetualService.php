<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\JurnalUmum;
use App\Models\JurnalDetail;
use App\Models\PenjualanNonKonsinyasi;
use App\Models\Pembayaran;

class JurnalPerpetualService
{
    public static function penjualanNonKonsinyasi(PenjualanNonKonsinyasi $penjualan): void
    {
        $nominal = (int) ($penjualan->total ?? 0);

        if ($nominal <= 0) {
            return;
        }

        $referensi = 'Jurnal Penjualan Non Konsinyasi ' . $penjualan->no_invoice;

        $jurnalLama = JurnalUmum::where('keterangan', $referensi)->first();

        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        $akunDebit = self::getAkunDebitPenjualan($penjualan);
        $akunPenjualan = Coa::where('kode_akun', '401')->firstOrFail();

        $jurnal = JurnalUmum::create([
            'tanggal'    => $penjualan->tanggal,
            'keterangan' => $referensi,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunDebit->id,
            'debit'          => $nominal,
            'kredit'         => 0,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunPenjualan->id,
            'debit'          => 0,
            'kredit'         => $nominal,
        ]);
    }

    protected static function getAkunDebitPenjualan(PenjualanNonKonsinyasi $penjualan): Coa
    {
        if ($penjualan->jenis_penjualan === 'MARKETPLACE') {
            return Coa::where('kode_akun', '113')->firstOrFail();
        }

        if (($penjualan->jenis_pembayaran ?? null) === 'transfer') {
            return Coa::where('kode_akun', '112')->firstOrFail();
        }

        if (($penjualan->jenis_pembayaran ?? null) === 'kredit') {
            return Coa::where('kode_akun', '130')->firstOrFail();
        }

        return Coa::where('kode_akun', '111')->firstOrFail();
    }

    public static function pembayaranNonKonsinyasi(Pembayaran $pembayaran): void
    {
        $nominal = (int) ($pembayaran->jumlah_bayar ?? 0);

        if ($nominal <= 0) {
            return;
        }

        $referensi = 'Jurnal Pembayaran Piutang ' . $pembayaran->kode_pembayaran;

        $jurnalLama = JurnalUmum::where('keterangan', $referensi)->first();

        if ($jurnalLama) {
            $jurnalLama->details()->delete();
            $jurnalLama->delete();
        }

        $akunDebit = self::getAkunDebitPembayaran($pembayaran);
        $akunPiutang = Coa::where('kode_akun', '130')->firstOrFail();

        $jurnal = JurnalUmum::create([
            'tanggal'    => $pembayaran->tanggal_bayar ?? now(),
            'keterangan' => $referensi,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunDebit->id,
            'debit'          => $nominal,
            'kredit'         => 0,
        ]);

        JurnalDetail::create([
            'jurnal_umum_id' => $jurnal->id,
            'akun_id'        => $akunPiutang->id,
            'debit'          => 0,
            'kredit'         => $nominal,
        ]);
    }

    protected static function getAkunDebitPembayaran(Pembayaran $pembayaran): Coa
    {
        if (($pembayaran->metode_pembayaran ?? null) === 'transfer') {
            return Coa::where('kode_akun', '112')->firstOrFail();
        }

        return Coa::where('kode_akun', '111')->firstOrFail();
    }
}