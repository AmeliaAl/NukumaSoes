<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtangJangkaPanjang extends Model
{
    use HasFactory;

    protected $table = 'utang_jangka_panjang';

    protected $fillable = [
        'tanggal',
        'nama_utang',
        'akun_id',
        'akun_debit_id',
        'nominal',
        'jatuh_tempo',
        'keterangan',
        'jurnal_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jatuh_tempo' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function akunKredit()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }

    public function akunDebit()
    {
        return $this->belongsTo(Akun::class, 'akun_debit_id');
    }

    /**
     * Relasi ke jurnal
     */
    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranUtangJangkaPanjang::class, 'utang_jangka_panjang_id');
    }

    public function getSisaUtangAttribute()
    {
        return $this->nominal - $this->pembayaran()->sum('nominal');
    }

    /**
     * Boot method untuk handle events
     */
    protected static function boot()
    {
        parent::boot();

        // Event setelah data dibuat
        static::created(function ($utang) {
            $utang->buatJurnal();
        });

        // Event sebelum data dihapus
        static::deleting(function ($utang) {
            // Hapus jurnal terkait jika ada
            if ($utang->jurnal_id) {
                $utang->jurnal()->delete();
            }
        });
    }

    /**
     * Buat jurnal otomatis
     */
    public function buatJurnal()
    {
        \DB::transaction(function () {
            // Buat header jurnal
            $jurnal = Jurnal::create([
                'tanggal' => $this->tanggal,
                'no_referensi' => 'UJP-' . $this->id,
                'deskripsi' => 'Utang Jangka Panjang: ' . $this->nama_utang,
            ]);

            // Buat detail jurnal - Debit (Kas/Aset)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun' => $this->akun_debit_id,
                'deskripsi' => 'Penerimaan dari ' . $this->nama_utang,
                'debit' => $this->nominal,
                'credit' => 0,
            ]);

            // Buat detail jurnal - Kredit (Utang Jangka Panjang)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id,
                'no_akun' => $this->akun_id,
                'deskripsi' => 'Utang: ' . $this->nama_utang,
                'debit' => 0,
                'credit' => $this->nominal,
            ]);

            // Update jurnal_id di utang
            $this->update(['jurnal_id' => $jurnal->id]);
        });
    }
}
