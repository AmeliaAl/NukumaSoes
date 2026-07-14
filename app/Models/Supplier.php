<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'telepon',
    ];

    protected static function booted()
    {
        static::creating(function ($supplier) {
            if (empty($supplier->kode_supplier)) {
                $lastSupplier = static::latest('id')->first();
                
                if ($lastSupplier && !empty($lastSupplier->kode_supplier)) {
                    $lastNumber = (int) substr($lastSupplier->kode_supplier, 3);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                
                $supplier->kode_supplier = 'SUP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}