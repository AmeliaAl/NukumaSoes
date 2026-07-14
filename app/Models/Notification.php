<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Notification — Placeholder integrasi modul Produksi
 *
 * Jenis notifikasi:
 *   'produksi_to_pembelian' → Produksi membuat Permintaan Bahan Baku
 *   'pembelian_to_produksi' → Pembelian selesai, kirim status ke Produksi
 *
 * TODO (setelah integrasi Produksi):
 *   - Tambah relasi ke model PermintaanProduksi
 *   - Tambah relasi ke model Pembelian (via reference_no)
 *   - Aktifkan pengiriman notifikasi otomatis dari controller
 */
class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'reference_no',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ── Scope ──────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
