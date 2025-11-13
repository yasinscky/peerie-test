<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
        'hashtag_blocks' => 'array',
        'guidelines' => 'array',
    ];
}
