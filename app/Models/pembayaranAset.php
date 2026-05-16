<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pembayaranAset extends Model
{
    use hasFactory;
    protected $table = 'pembayaran_aset';
    protected $fillable = [
        'id_faktur',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_pembayaran',
        'id_akun',
        'keterangan',
        'tgl_terima_brg',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    protected $appends = [
        'total_tagihan',
        'total_terbayar',
        'sisa_tagihan',
    ];

    protected static function booted()
    {
        static::created(function ($p) {

        // update status faktur
        $p->faktur->updateStatus();

        // kurangi saldo akun
        if ($p->id_akun) {
            $akun = Akun::find($p->id_akun);

            /*if ($akun) {
                $akun->saldo -= $p->jumlah_bayar;
                $akun->save();
            }*/
        }

    });
        static::updated(fn ($p) => $p->faktur->updateStatus());
        static::deleted(fn ($p) => $p->faktur->updateStatus());
    }

    public function faktur()
    {
        return $this->belongsTo(FakturPembelian::class, 'id_faktur');
    }

    public function getTotalTagihanAttribute(): float
    {
        return $this->faktur->total_tagihan ?? 0;
    }

    public function getTotalTerbayarAttribute(): float
    {
        return $this->faktur->pembayaran()->sum('jumlah_bayar');
    }

    public function getSisaTagihanAttribute(): float
    {
        return max(0, $this->total_tagihan - $this->total_terbayar);
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun');
    }
}
