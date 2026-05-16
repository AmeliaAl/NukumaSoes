<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BukuBesar extends Model
{ 
    use HasFactory;
     protected $table = 'jurnal'; // Nama tabel eksplisit

    // // relasi ke jurnal detail
    public function jurnaldetail()
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal');
    }
}
