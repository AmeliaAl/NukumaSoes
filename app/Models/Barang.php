<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'rasa',
        'satuan',
        'stok_awal',
        'harga_barang',
        'stok',
        'foto',
    ];

    protected static function booted(): void
    {
        static::creating(function ($barang) {
            if (blank($barang->stok)) {
                $barang->stok = (int) ($barang->stok_awal ?? 0);
            }
        });
    }

    public static function getKodeBarang()
    {
        $sql = "SELECT IFNULL(MAX(kode_barang), 'AB000') as kode_barang FROM barang";
        $result = DB::select($sql);

        $kode = $result[0]->kode_barang;
        $no = (int) substr($kode, -3) + 1;

        return 'AB' . str_pad($no, 3, '0', STR_PAD_LEFT);
    }

    public function setHargaBarangAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['harga_barang'] = 0;
            return;
        }

        $this->attributes['harga_barang'] = (int) str_replace(['.', ','], '', $value);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function hargaBarang(): HasMany
    {
        return $this->hasMany(HargaBarang::class, 'barang_id');
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(
            DetailPenjualanNonKonsinyasi::class,
            'barang_id'
        );
    }

    public function getNamaLengkapAttribute(): string
    {
        $parts = [
            $this->nama_barang,
            $this->rasa,
            optional($this->kategori)->nama_kategori,
        ];

        return collect($parts)
            ->filter(fn ($item) => filled($item))
            ->implode(' - ');
    }

    public function getHargaByJenisMitra(?string $jenisMitra): int
{
    if (! $jenisMitra) {
        return 0;
    }

    $harga = $this->hargaBarang()
        ->where('jenis_mitra', $jenisMitra)
        ->value('harga');

    // fallback ke reseller
    if (! $harga) {
        $harga = $this->hargaBarang()
            ->where('jenis_mitra', 'reseller')
            ->value('harga');
    }

    return (int) ($harga ?? 0);
}
}