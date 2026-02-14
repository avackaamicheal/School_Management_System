<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Term extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = ['academic_session_id', 'name', 'is_active'];

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    // Custom method to safely activate this term
    public function makeActive()
    {
        DB::transaction(function () {
            // 1. Deactivate ALL other terms for this specific school
            self::where('school_id', $this->school_id)->update(['is_active' => false]);

            // 2. Activate this one
            $this->update(['is_active' => true]);
        });
    }
}
