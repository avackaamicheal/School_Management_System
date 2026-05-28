<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSubscription extends Model
{
    protected $fillable = [
        'school_id',
        'plan_id',
        'amount_paid',
        'paystack_reference',
        'status',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function isInGracePeriod(): bool
    {
        return $this->expires_at->isPast()
            && $this->expires_at->diffInDays(now()) <= 7;
    }

    public function daysUntilExpiry(): int
    {
        return (int) now()->diffInDays($this->expires_at, false);
    }
}
