<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry',
        'country',
        'language',
        'title',
        'intro_title',
        'intro_description',
        'guidelines',
        'tags',
        'hashtag_blocks',
    ];

    protected $casts = [
        'tags' => 'array',
        'hashtag_blocks' => 'array',
        'guidelines' => 'array',
    ];
}
