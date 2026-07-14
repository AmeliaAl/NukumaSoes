<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Akun
 * Tabel: akun
 * Struktur sesuai database utama tim:
 *   id, header_akun, no_akun, nama_akun, created_at, updated_at
 */
class Akun extends Model
{
    protected $table = 'akun';

    protected $fillable = [
        'header_akun',
        'no_akun',
        'nama_akun',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────

    /** Akun hasMany JurnalDetail */
    public function jurnalDetails()
    {
        return $this->hasMany(JurnalDetail::class, 'no_akun', 'no_akun');
    }
}
