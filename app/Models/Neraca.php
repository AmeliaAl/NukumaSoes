<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neraca extends Model
{
    protected $table = 'jurnal'; 
    // // relasi ke jurnal detail
    public function jurnaldetail()
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal');
    }
    
}
