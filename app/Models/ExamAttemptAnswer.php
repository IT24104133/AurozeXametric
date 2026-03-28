<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',

        // Option A: store chosen option by ID (NOT A/B/C/D)
        'selected_option_id',

        // legacy fallback (A/B/C/D)
        'selected_option',

        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    /**
     * Return the selected option key (A/B/C/D).
     * Priority:
     * 1) If legacy selected_option exists, return it
     * 2) Else, use selected_option_id -> question_options.option_key
     */
    public function getSelectedOptionAttribute($value)
    {
        // If legacy selected_option (A/B/C/D) exists, return it
        if (!empty($value)) {
            return $value;
        }

        // If no selected_option_id, nothing selected
        if (empty($this->selected_option_id)) {
            return null;
        }

        // IMPORTANT: call relationship as METHOD to avoid accessor recursion error
        $opt = $this->relationLoaded('selectedOption')
            ? $this->getRelation('selectedOption')
            : $this->selectedOption()->first();

        return $opt ? $opt->option_key : null;
    }
}
