<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Jurnal — header transaksi jurnal.
 * Tabel: jurnal
 * Kolom: id, tanggal, no_referensi, deskripsi, created_at, updated_at
 */
class Jurnal extends Model
{
    protected $table = 'jurnal';

    protected $fillable = [
        'tanggal',
        'no_referensi',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────

    /**
     * Jurnal hasMany JurnalDetail
     */
    public function details()
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal');
    }
}
