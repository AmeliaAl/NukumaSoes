<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BahanBaku;

class BahanBaku extends Model
{
    protected $fillable = [
    'kode_bahan',
    'nama_bahan',
    'satuan',
];
}
