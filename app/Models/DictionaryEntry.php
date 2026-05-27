<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DictionaryEntry extends Model
{
    protected $fillable = [
        'word',
        'normalized_word',
        'part_of_speech',
        'definition',
        'definition_vi',
        'examples',
        'synonyms',
        'source',
        'source_id',
    ];

    protected $casts = [
        'examples' => 'array',
        'synonyms' => 'array',
    ];
}
