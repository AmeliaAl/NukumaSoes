<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class statusAset extends Model
{
    use HasFactory;

    protected $table = 'status_aset';

    protected $fillable = [
        'kode_status',
        'nama_status',
    ];
}
