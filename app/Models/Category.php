<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nama_kategori', 'berat', 'jenis_kemasan', 'masa_simpan', 'satuan_masa_simpan', 'deskripsi', 'harga'];
}
