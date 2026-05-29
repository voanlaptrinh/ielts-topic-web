<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'test_type',
        'level',
        'score',
        'total',
        'band_score',
        'feedback',
        'criteria_scores',
        'reviewed_by',
        'reviewed_at',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'criteria_scores' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function needsHumanReview(): bool
    {
        return in_array($this->test_type, ['IELTS Writing', 'IELTS Speaking'], true) && ! $this->reviewed_at;
    }
}
