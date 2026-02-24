<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeRecord extends Model
{

    use HasFactory, Multitenantable;
    protected $fillable = [
        'term_id',
        'section_id',
        'subject_id',
        'student_id',
        'scores',
        'total_score',
        'is_locked'
    ];

    // Tell Laravel to automatically convert the JSON to a PHP Array
    protected $casts = [
        'scores' => 'array',
        'is_locked' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

}
