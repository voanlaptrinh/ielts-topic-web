<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WordLookupHistory extends Model
{
    protected $fillable = [
        'user_id',
        'word',
        'normalized_word',
        'senses_count',
    ];
}
