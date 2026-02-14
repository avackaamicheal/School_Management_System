<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AcademicSession extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    // Custom method to safely activate this session
    public function makeActive()
    {
        DB::transaction(function () {
            // 1. Deactivate ALL other sessions for this specific school
            self::where('school_id', $this->school_id)->update(['is_active' => false]);

            // 2. Activate this one
            $this->update(['is_active' => true]);
        });
    }
}
