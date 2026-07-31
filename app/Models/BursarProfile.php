<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class BursarProfile extends Model
{
    use Multitenantable;

    protected $fillable = [
        'user_id',
        'school_id',
        'employee_id',
        'phone',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateEmployeeId(): string
    {
        $school = School::find(session('active_school'));

        $schoolInitials = collect(explode(' ', $school->name))
            ->map(fn($word) => strtoupper($word[0]))
            ->implode('');

        $prefix = $schoolInitials . '-BUR';
        $year = date('Y');

        $last = static::withoutGlobalScopes()
            ->where('school_id', session('active_school'))
            ->whereYear('created_at', $year)
            ->where('employee_id', 'like', "{$prefix}%")
            ->latest()
            ->first();

        if ($last && $last->employee_id) {
            $lastNumber = (int) substr($last->employee_id, -4);
            $next = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return "{$prefix}-{$year}-{$next}";
    }
}
