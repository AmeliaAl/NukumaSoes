<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaldoAwal extends Model
{
    protected $table = 'saldo_awals';
    protected $guarded = [];

    public function coa()
    {
        return $this->belongsTo(coa::class, 'coa_id');
    }

    /**
     * Hitung saldo awal efektif untuk akun pada bulan/tahun tertentu.
     *
     * - Jika ada entri eksplisit → pakai nominal-nya.
     * - Jika tidak ada → ambil saldo akhir bulan sebelumnya
     *   (saldo awal pertama tercatat + semua mutasi jurnal s/d akhir bulan lalu).
     */
    public static function getSaldoEfektif(int $coaId, int $bulan, int $tahun): float
    {
        $entri = self::where('coa_id', $coaId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if ($entri) {
            return (float) $entri->nominal;
        }

        $tglAkhirBulanLalu = Carbon::createFromDate($tahun, $bulan, 1)
            ->subDay()
            ->toDateString();

        $saldoAwalTercatat = self::where('coa_id', $coaId)
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->first();

        if (! $saldoAwalTercatat) {
            return 0.0;
        }

        $tglMulai = Carbon::createFromDate(
            $saldoAwalTercatat->tahun,
            $saldoAwalTercatat->bulan,
            1
        )->toDateString();

        $mutasi = DB::table('jurnal_detail')
            ->join('jurnal_umum', 'jurnal_detail.jurnal_umum_id', '=', 'jurnal_umum.id')
            ->where('jurnal_detail.akun_id', $coaId)
            ->whereDate('jurnal_umum.tanggal', '>=', $tglMulai)
            ->whereDate('jurnal_umum.tanggal', '<=', $tglAkhirBulanLalu)
            ->selectRaw('SUM(debit) - SUM(kredit) as net')
            ->value('net');

        return (float) $saldoAwalTercatat->nominal + (float) $mutasi;
    }

    protected static function booted(): void
    {
        static::saved(function (SaldoAwal $saldoAwal) {
            \App\Services\JurnalPerpetualService::saldoAwal($saldoAwal);
        });

        static::deleted(function (SaldoAwal $saldoAwal) {
            \App\Services\JurnalPerpetualService::hapusSaldoAwal($saldoAwal);
        });
    }
}
