<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentIdea extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';
    protected $table = 'content_ideas';

    protected $fillable = [
        'date',
        'title',
        'caption',
        'hashtags',
        'tips',
        'language',
        'audiences',
    ];

    protected $casts = [
        'date' => 'date',
        'audiences' => 'array',
    ];
}
