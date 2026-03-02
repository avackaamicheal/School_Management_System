<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use Multitenantable;

    protected $fillable = ['term_id', 'class_level_id', 'name', 'amount'];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
    public function classLevel()
    {
        return $this->belongsTo(ClassLevel::class);
    }
}
