<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coa;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';
    protected $guarded = [];

    public function jurnal()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_umum_id');
    }

    public function akun()
    {
        return $this->belongsTo(Coa::class, 'akun_id');
    }
}
