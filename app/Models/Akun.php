<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'tipe_akun',
        'saldo_normal',
        'saldo',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    // Relasi: Akun punya banyak detail jurnal
    public function jurnalDetail()
    {
        return $this->hasMany(JurnalUmumDetail::class, 'id_akun', 'id_akun');
    }

    // Helper: Hitung total debit
    public function getDebitTotal($tanggalMulai = null, $tanggalAkhir = null)
    {
        $query = $this->jurnalDetail();
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereHas('jurnalUmum', function ($q) use ($tanggalMulai, $tanggalAkhir) {
                $q->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            });
        }
        return $query->sum('debit');
    }

    // Helper: Hitung total kredit
    public function getKreditTotal($tanggalMulai = null, $tanggalAkhir = null)
    {
        $query = $this->jurnalDetail();
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereHas('jurnalUmum', function ($q) use ($tanggalMulai, $tanggalAkhir) {
                $q->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            });
        }
        return $query->sum('kredit');
    }

    // Helper: Hitung saldo berjalan
    public function hitungSaldo($tanggalMulai = null, $tanggalAkhir = null)
    {
        $totalDebit = $this->getDebitTotal($tanggalMulai, $tanggalAkhir);
        $totalKredit = $this->getKreditTotal($tanggalMulai, $tanggalAkhir);

        if ($this->saldo_normal === 'debit') {
            return $totalDebit - $totalKredit;
        }
        return $totalKredit - $totalDebit;
    }

    // Scope: By tipe
    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe_akun', $tipe);
    }

    // Scope: Aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Helper: Get tipe akun list
    public static function getTipeAkunList()
    {
        return [
            'aset' => 'Aset',
            'kewajiban' => 'Kewajiban',
            'ekuitas' => 'Ekuitas',
            'pendapatan' => 'Pendapatan',
            'beban' => 'Beban',
        ];
    }
}
