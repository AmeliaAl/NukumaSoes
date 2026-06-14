<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmumDetail extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum_detail';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_jurnal',
        'id_akun',
        'debit',
        'kredit',
        'keterangan',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    // Relasi: Detail ini milik jurnal tertentu
    public function jurnalUmum()
    {
        return $this->belongsTo(JurnalUmum::class, 'id_jurnal', 'id_jurnal');
    }

    // Relasi: Detail ini untuk akun tertentu
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }
}
