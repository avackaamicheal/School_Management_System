<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use Multitenantable;

    protected $fillable = [
        'author_id',
        'title',
        'content',
        'target_role',
        'publish_at',
        'expires_at'
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
