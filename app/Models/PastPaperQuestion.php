<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PastPaperQuestion extends Model
{
    protected $table = 'past_paper_questions';

    protected $fillable = [
        'past_paper_id',
        'question_text',
        'difficulty',
        'times_used',
        'last_used_at',
        'question_image',
        'question_image_1',
        'question_image_2',
        'question_image_3',
        'explanation',
        'weight',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the past paper this question belongs to.
     */
    public function pastPaper(): BelongsTo
    {
        return $this->belongsTo(PastPaper::class, 'past_paper_id');
    }

    /**
     * Get all options for this question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(PastPaperOption::class, 'question_id')->orderBy('position');
    }
}
