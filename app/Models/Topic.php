<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'part',
        'difficulty',
        'questions',
        'sample_answer',
        'tips',
    ];

    protected $casts = [
        'questions' => 'array',
        'tips' => 'array',
    ];
}
