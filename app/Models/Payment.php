<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use Multitenantable;

    protected $fillable = ['invoice_id', 'amount', 'method', 'reference', 'payment_date'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
