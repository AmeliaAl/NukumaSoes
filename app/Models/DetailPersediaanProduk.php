<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class DetailPersediaanProduk extends Model
{
    protected $table = 'detail_persediaan_produk';

    protected $fillable = [
        'barang_id',
        'batch',
        'stok_awal',
        'stok_saat_ini',
        'harga_modal_per_pack',
        'tanggal_expired',
    ];

    protected $casts = [
        'tanggal_expired'      => 'date',
        'stok_awal'            => 'integer',
        'stok_saat_ini'        => 'integer',
        'harga_modal_per_pack' => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        // Saat record baru dibuat, stok_saat_ini = stok_awal jika belum diset
        static::creating(function ($model) {
            if (blank($model->stok_saat_ini)) {
                $model->stok_saat_ini = (int) ($model->stok_awal ?? 0);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * FEFO: batch dengan tanggal_expired paling dekat lebih dulu.
     * Batch tanpa tanggal_expired diletakkan paling akhir.
     */
    public function scopeFefo($query)
    {
        return $query
            ->orderByRaw('tanggal_expired IS NULL ASC')
            ->orderBy('tanggal_expired', 'asc');
    }

    /** Hanya batch yang masih ada stok. */
    public function scopeAvailable($query)
    {
        return $query->where('stok_saat_ini', '>', 0);
    }

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    // -------------------------------------------------------------------------
    // FEFO Stock Operations
    // -------------------------------------------------------------------------

    /**
     * Kurangi stok batch-batch milik $barangId secara FEFO dan kembalikan data HPP.
     *
     * Return value: array dengan dua kunci:
     *   - harga_modal_per_pack : float  — HPP rata-rata tertimbang (total_hpp / qty)
     *   - subtotal_hpp         : float  — total HPP = SUM(qty_keluar × hpp_batch)
     *
     * Aturan:
     * - Batch dengan tanggal_expired paling dekat dikurangi lebih dulu.
     * - Batch tanpa tanggal_expired (null) dipakai paling akhir.
     * - Hanya batch yang belum expired dan stok_saat_ini > 0 yang disentuh.
     * - Harus dipanggil di dalam DB::transaction dari caller agar atomik.
     *
     * @throws \RuntimeException jika stok tidak mencukupi
     * @return array{harga_modal_per_pack: float, subtotal_hpp: float}
     */
    public static function kurangiStokFefo(int $barangId, int $qty): array
    {
        if ($qty <= 0) {
            return ['harga_modal_per_pack' => 0.0, 'subtotal_hpp' => 0.0];
        }

        $batches = self::where('barang_id', $barangId)
            ->where('stok_saat_ini', '>', 0)
            ->where(function ($q) {
                $q->whereNull('tanggal_expired')
                    ->orWhere('tanggal_expired', '>=', now()->toDateString());
            })
            ->orderByRaw('tanggal_expired IS NULL ASC')
            ->orderBy('tanggal_expired', 'asc')
            ->lockForUpdate()
            ->get();

        $sisa        = $qty;
        $subtotalHpp = 0.0;

        foreach ($batches as $batch) {
            if ($sisa <= 0) {
                break;
            }

            $ambil       = min($batch->stok_saat_ini, $sisa);
            $hpp         = (float) $batch->harga_modal_per_pack;
            $subtotalHpp += $ambil * $hpp;

            $batch->decrement('stok_saat_ini', $ambil);
            $sisa -= $ambil;
        }

        if ($sisa > 0) {
            throw new \RuntimeException(
                "Stok tidak mencukupi. Kekurangan: {$sisa} unit."
            );
        }

        // HPP rata-rata tertimbang dari semua batch yang keluar
        $hppRataRata = $qty > 0 ? round($subtotalHpp / $qty, 2) : 0.0;

        return [
            'harga_modal_per_pack' => $hppRataRata,
            'subtotal_hpp'         => round($subtotalHpp, 2),
        ];
    }

    /**
     * Hitung HPP FEFO tanpa mengubah stok (dry-run).
     *
     * Digunakan saat edit qty untuk menghitung ulang HPP dari selisih qty.
     *
     * @return array{harga_modal_per_pack: float, subtotal_hpp: float}
     */
    public static function hitungHppFefo(int $barangId, int $qty): array
    {
        if ($qty <= 0) {
            return ['harga_modal_per_pack' => 0.0, 'subtotal_hpp' => 0.0];
        }

        $batches = self::where('barang_id', $barangId)
            ->where('stok_saat_ini', '>', 0)
            ->where(function ($q) {
                $q->whereNull('tanggal_expired')
                    ->orWhere('tanggal_expired', '>=', now()->toDateString());
            })
            ->orderByRaw('tanggal_expired IS NULL ASC')
            ->orderBy('tanggal_expired', 'asc')
            ->get();

        $sisa        = $qty;
        $subtotalHpp = 0.0;

        foreach ($batches as $batch) {
            if ($sisa <= 0) {
                break;
            }

            $ambil       = min($batch->stok_saat_ini, $sisa);
            $subtotalHpp += $ambil * (float) $batch->harga_modal_per_pack;
            $sisa        -= $ambil;
        }

        $hppRataRata = $qty > 0 ? round($subtotalHpp / $qty, 2) : 0.0;

        return [
            'harga_modal_per_pack' => $hppRataRata,
            'subtotal_hpp'         => round($subtotalHpp, 2),
        ];
    }

    /**
     * Kembalikan stok ke batch-batch milik $barangId (kebalikan FEFO).
     *
     * - Batch dengan tanggal_expired paling jauh diisi lebih dulu.
     * - Hanya batch yang belum expired yang diisi ulang.
     * - Pengembalian dibatasi oleh stok_awal masing-masing batch.
     * - Harus dipanggil di dalam DB::transaction dari caller.
     * - Jika tidak ada batch tersedia, lewati saja (aman untuk transaksi lama).
     */
    public static function kembalikanStokFefo(int $barangId, int $qty): void
    {
        if ($qty <= 0) {
            return;
        }

        $batches = self::where('barang_id', $barangId)
            ->where(function ($q) {
                $q->whereNull('tanggal_expired')
                    ->orWhere('tanggal_expired', '>=', now()->toDateString());
            })
            ->orderByRaw('tanggal_expired IS NULL DESC')
            ->orderBy('tanggal_expired', 'desc')
            ->lockForUpdate()
            ->get();

        if ($batches->isEmpty()) {
            return;
        }

        $sisa = $qty;

        foreach ($batches as $batch) {
            if ($sisa <= 0) {
                break;
            }

            $ruang = $batch->stok_awal - $batch->stok_saat_ini;

            if ($ruang <= 0) {
                continue;
            }

            $kembalikan = min($sisa, $ruang);
            $batch->increment('stok_saat_ini', $kembalikan);
            $sisa -= $kembalikan;
        }
    }
}
