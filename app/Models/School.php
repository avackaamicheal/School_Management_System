<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'email',
        'address',
        'principal_name',
        'phone_number',
        'is_active',
        'approval_status',
        'rejection_reason',
        'approved_at',
        'is_subscribed',
        'subscription_expires_at'
    ];


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });
    }

    // 4. Tell Laravel to use this column for URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'subscription_expires_at' => 'datetime',
    ];

    public function subscriptions()
    {
        return $this->hasMany(SchoolSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(SchoolSubscription::class)
            ->where('status', 'active')
            ->latest();
    }

    public function hasActiveSubscription(): bool
    {
        $sub = $this->activeSubscription()->first();
        if (!$sub)
            return false;
        return $sub->isActive() || $sub->isInGracePeriod();
    }

    public function isInGracePeriod(): bool
    {
        $sub = $this->activeSubscription()->first();
        return $sub?->isInGracePeriod() ?? false;
    }

    public function setupStatus(): array
    {
        $schoolId = $this->id;

        return [
            'has_session' => \App\Models\AcademicSession::where('school_id', $schoolId)->exists(),
            'has_term' => \App\Models\Term::where('school_id', $schoolId)->exists(),
            'has_class' => \App\Models\ClassLevel::where('school_id', $schoolId)->exists(),
            'has_section' => \App\Models\Section::where('school_id', $schoolId)->exists(),
            'has_subject' => \App\Models\Subject::where('school_id', $schoolId)->exists(),
        ];
    }

    public function isSetupComplete(): bool
    {
        return !in_array(false, $this->setupStatus(), true);
    }


}
