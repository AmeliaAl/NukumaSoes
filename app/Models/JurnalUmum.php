<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    protected $fillable = [
        'tanggal',
        'keterangan',
        'ref',
        'debit',
        'kredit',
        'id_transaksi',
    ];
    protected $table = 'jurnal_umums';
    protected $guarded = [];

    protected static function booted()
    {
        /*static::creating(function ($jurnal) {
            $date = now()->format('Ym');

            $last = self::where('no_jurnal', 'like', "JU-{$date}-%")
                ->orderByDesc('id')
                ->value('no_jurnal');

            $urut = $last ? ((int) substr($last, -3)) + 1 : 1;

            $jurnal->no_jurnal = "JU-{$date}-" . str_pad($urut, 3, '0', STR_PAD_LEFT);
        });*/
    }
    
    public function details()
    {
        return $this->hasMany(JurnalDetail::class, 'jurnal_umum_id');
    }

    public function getTotalAttribute()
    {
        return $this->details()->sum('debit');
    }

}
