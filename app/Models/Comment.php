<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, Likeable;

    protected $fillable = [
        'body',
        'user_id'
    ];

    //
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
