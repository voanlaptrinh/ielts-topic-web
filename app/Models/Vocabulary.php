<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    protected $fillable = [
        'word',
        'phonetic',
        'part_of_speech',
        'meaning_vi',
        'definition_en',
        'example_en',
        'example_vi',
        'topic',
        'level',
        'synonyms',
    ];

    protected $casts = [
        'synonyms' => 'array',
    ];
}
