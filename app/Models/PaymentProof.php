<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    protected $fillable = [
        'proofable_id',
        'proofable_type',
        'file_path',
        'file_name',
    ];

    public function proofable()
    {
        return $this->morphTo();
    }
}
