<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBop extends Model
{
    use HasFactory;

    private const ASSET_RELATED_KEYWORDS = [
        'mesin',
        'penyusutan',
        'aset',
    ];

    protected $table = 'kategori_bop';
    protected $primaryKey = 'id_kategori_bop';

    protected $fillable = [
        'nama_kategori',
        'id_akun',
        'keterangan',
    ];

    // Relasi: Kategori BOP ini dipakai di banyak transaksi BOP
    public function biayaOverheadPabrik()
    {
        return $this->hasMany(BiayaOverheadPabrik::class, 'id_kategori_bop', 'id_kategori_bop');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }

    public function scopeProductionScope($query)
    {
        foreach (self::ASSET_RELATED_KEYWORDS as $keyword) {
            $query->where('nama_kategori', 'not like', '%' . $keyword . '%');
        }

        return $query;
    }

    public static function isAssetRelatedName(string $name): bool
    {
        $normalized = strtolower($name);

        foreach (self::ASSET_RELATED_KEYWORDS as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
