<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
     use HasFactory;

    protected $table = 'akun'; // Nama tabel eksplisit

    protected $guarded = [];
    protected $fillable = [
        'header_akun',
        'no_akun',
        'nama_akun',
        'saldo',
    ];

    

    public function jurnalDetail()
    {
        return $this->hasMany(JurnalDetail::class, 'no_akun', 'id');
    }

    public function kategoriAset()
{
    return $this->belongsTo(KategoriAset::class, 'id_kategori');
}

public function pembayaran()
{
    return $this->hasMany(PembayaranAset::class, 'id_akun');
}

public function pemeliharaan()
{
    return $this->hasMany(Pemeliharaan::class, 'id_akun');
}

public function utangJangkaPanjangKredit()
{
    return $this->hasMany(UtangJangkaPanjang::class, 'akun_id');
}

public function utangJangkaPanjangDebit()
{
    return $this->hasMany(UtangJangkaPanjang::class, 'akun_debit_id');
}

}
