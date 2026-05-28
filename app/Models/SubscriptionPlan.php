<?php

namespace App\Models;

use App\Models\SchoolSubscription;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'max_students', 'price', 'duration_days', 'is_active'];

    public function subscriptions()
    {
        return $this->hasMany(SchoolSubscription::class, 'plan_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price);
    }
}
