<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeQuestion extends Model
{
    protected $fillable = [
        'practice_test_id',
        'position',
        'question_type',
        'prompt',
        'options',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
        'position' => 'integer',
    ];

    public function practiceTest(): BelongsTo
    {
        return $this->belongsTo(PracticeTest::class);
    }
}
