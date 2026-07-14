<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model JurnalDetail — detail entri debit/kredit.
 * Tabel: jurnal_detail
 * Kolom: id, id_jurnal, no_akun, deskripsi, debit, credit, created_at, updated_at
 */
class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';

    protected $fillable = [
        'id_jurnal',
        'no_akun',
        'deskripsi',
        'debit',
        'credit',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────

    /**
     * JurnalDetail belongsTo Jurnal
     */
    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }

    /**
     * JurnalDetail belongsTo Akun (via no_akun string key)
     */
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'no_akun', 'no_akun');
    }
}
