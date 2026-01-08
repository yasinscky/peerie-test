<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'language',
        'file_path',
        'original_filename',
        'sort_order',
        'published_at',
    ];
}
