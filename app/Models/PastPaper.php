<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PastPaper extends Model
{
    protected $table = 'past_papers';

    protected $fillable = [
        'subject_id',
        'category',
        'year',
        'title',
        'description',
        'duration_minutes',
        'status',
        'stream',
        'total_questions',
        'count_e',
        'count_s',
        'count_m',
        'count_h',
    ];

    protected $casts = [
        'year' => 'integer',
        'duration_minutes' => 'integer',
        'total_questions' => 'integer',
        'count_e' => 'integer',
        'count_s' => 'integer',
        'count_m' => 'integer',
        'count_h' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the subject this past paper belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(PastPaperSubject::class, 'subject_id');
    }

    /**
     * Get all questions for this past paper.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(PastPaperQuestion::class, 'past_paper_id');
    }

    /**
     * Get all attempts for this past paper.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(PastPaperAttempt::class, 'past_paper_id');
    }
}
