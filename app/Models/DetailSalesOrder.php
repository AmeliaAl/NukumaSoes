<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSalesOrder extends Model
{
    use HasFactory;

    protected $table = 'detail_sales_orders';
    protected $guarded = [];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
