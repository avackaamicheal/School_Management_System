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

    // payment rellationship
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helper: Calculate total paid
    public function amountPaid()
    {
        return $this->payments()->sum('amount');
    }

    // Helper: Calculate remaining balance
    public function balance()
    {
        return $this->total_amount - $this->amountPaid();
    }
}
