<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'test_type',
        'level',
        'score',
        'total',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}
