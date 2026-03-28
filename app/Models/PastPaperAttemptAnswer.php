<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PastPaperAttemptAnswer extends Model
{
    protected $table = 'past_paper_attempt_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the attempt this answer belongs to.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PastPaperAttempt::class, 'attempt_id');
    }

    /**
     * Get the question this answer is for.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(PastPaperQuestion::class, 'question_id');
    }

    /**
     * Get the selected option.
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(PastPaperOption::class, 'selected_option_id');
    }
}
