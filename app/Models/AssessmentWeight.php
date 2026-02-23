<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentWeight extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = ['subject_id', 'name', 'weight'];

    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
