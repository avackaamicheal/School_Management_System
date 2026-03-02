<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use Multitenantable;

    protected $fillable = ['term_id', 'student_id', 'invoice_number', 'total_amount', 'status', 'due_date'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
