<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'wordpress_id',
        'content',
        'author',
        'polarity_score',
        'status',
    ];
}
