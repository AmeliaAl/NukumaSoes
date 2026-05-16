<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class lokasiAset extends Model
{
    use HasFactory;
    protected $table = 'lokasi_aset';
    protected $fillable = [
        'kode_lokasi',
        'nama_lokasi',
    ];

    public static function generateKdLokasi()
    {
        // 1. Ambil No Faktur terakhir dari database
        $lastLokasi = self::query()
                       ->latest('id') // Ambil data terakhir berdasarkan ID
                       ->first();

        $prefix = 'LA-'; // Prefix yang diinginkan
        $nextNumber = 1;

        if ($lastLokasi && $lastLokasi->kode_lokasi) {
            // 2. Ekstrak bagian angka dari No Faktur terakhir (misalnya, dari PGJ-0012, ambil 12)
            $lastNumber = (int) substr($lastLokasi->kode_lokasi, 3); // Ambil dari karakter ke-5 (setelah 'PGJ-')
            $nextNumber = $lastNumber + 1;
        }

        // 3. Format angka menjadi string 4 digit (0001, 0002, dst.)
        $paddedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // 4. Gabungkan Prefix dan Angka
        return $prefix . $paddedNumber;
    }
}
