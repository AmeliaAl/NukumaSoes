<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalesOrder extends Model
{
    use HasFactory;

    protected $table = 'sales_orders';
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Validasi: tidak boleh ada SO ganda untuk referensi yang sama
    public static function boot()
    {
        parent::boot();

        static::creating(function ($salesOrder) {
            // Cek apakah sudah ada SO dengan referensi yang sama
            $exists = self::where('referensi', $salesOrder->referensi)->exists();
            
            if ($exists) {
                throw new \Exception("Sales Order dengan referensi {$salesOrder->referensi} sudah ada.");
            }
        });
    }

    public function detailSalesOrder()
    {
        return $this->hasMany(DetailSalesOrder::class);
    }

    public function penjualanNonKonsinyasi()
    {
        return $this->belongsTo(PenjualanNonKonsinyasi::class)->with('pelanggan');
    }

    public function penjualanKonsinyasi()
    {
        return $this->belongsTo(PenjualanKonsinyasi::class)->with('mitra');
    }

    public static function generateNoSO(): string
    {
        $date = Carbon::now()->format('Ymd');

        $last = self::whereDate('created_at', today())
            ->orderByDesc('id')
            ->value('no_so');

        if (!$last) {
            return "SO-{$date}-001";
        }

        $lastNumber = (int) substr($last, -3);
        $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "SO-{$date}-{$next}";
    }

    public static function createFromPenjualanNonKonsinyasi(PenjualanNonKonsinyasi $penjualan): self
    {
        // Cek apakah sudah ada SO untuk penjualan ini
        if (self::where('referensi', $penjualan->no_invoice)->exists()) {
            return self::where('referensi', $penjualan->no_invoice)->first();
        }

        $salesOrder = self::create([
            'no_so' => self::generateNoSO(),
            'tanggal' => now(),
            'referensi' => $penjualan->no_invoice,
            'jenis' => 'Non Konsinyasi',
            'status' => 'Draft',
            'penjualan_non_konsinyasi_id' => $penjualan->id,
        ]);

        // Copy detail penjualan ke detail sales order
        foreach ($penjualan->detailPenjualan as $detail) {
            DetailSalesOrder::create([
                'sales_order_id' => $salesOrder->id,
                'barang_id' => $detail->barang_id,
                'qty' => $detail->qty,
                'harga' => $detail->harga ?? 0,
                'subtotal' => $detail->subtotal ?? 0,
            ]);
        }

        return $salesOrder;
    }

    public static function createFromPenjualanKonsinyasi(PenjualanKonsinyasi $penjualan): self
    {
        // Jika SO sudah ada, sync ulang detail-nya
        $existing = self::where('referensi', $penjualan->no_konsinyasi)->first();

        if ($existing) {
            // Hapus detail lama lalu copy ulang dari detail konsinyasi terbaru
            $existing->detailSalesOrder()->delete();

            foreach ($penjualan->detailKonsinyasi as $detail) {
                DetailSalesOrder::create([
                    'sales_order_id' => $existing->id,
                    'barang_id'      => $detail->barang_id,
                    'qty'            => $detail->qty_titip,
                    'harga'          => $detail->harga_konsinyasi ?? 0,
                    'subtotal'       => ($detail->qty_titip * ($detail->harga_konsinyasi ?? 0)),
                ]);
            }

            return $existing;
        }

        $salesOrder = self::create([
            'no_so'                   => self::generateNoSO(),
            'tanggal'                 => now(),
            'referensi'               => $penjualan->no_konsinyasi,
            'jenis'                   => 'Konsinyasi',
            'status'                  => 'Draft',
            'penjualan_konsinyasi_id' => $penjualan->id,
        ]);

        foreach ($penjualan->detailKonsinyasi as $detail) {
            DetailSalesOrder::create([
                'sales_order_id' => $salesOrder->id,
                'barang_id'      => $detail->barang_id,
                'qty'            => $detail->qty_titip,
                'harga'          => $detail->harga_konsinyasi ?? 0,
                'subtotal'       => ($detail->qty_titip * ($detail->harga_konsinyasi ?? 0)),
            ]);
        }

        return $salesOrder;
    }
}
