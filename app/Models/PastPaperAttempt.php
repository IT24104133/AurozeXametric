<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PastPaperAttempt extends Model
{
    protected $table = 'past_paper_attempts';

    protected $fillable = [
        'past_paper_id',
        'student_id',
        'attempt_no',
        'status',
        'mode',
        'started_at',
        'ended_at',
        'score',
        'total_questions',
        'correct_count',
        'percentage',
        'score_percent',
        'question_order',
    ];

    protected $casts = [
        'attempt_no' => 'integer',
        'score' => 'integer',
        'total_questions' => 'integer',
        'correct_count' => 'integer',
        'percentage' => 'float',
        'score_percent' => 'float',
        'question_order' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the past paper this attempt is for.
     */
    public function pastPaper(): BelongsTo
    {
        return $this->belongsTo(PastPaper::class, 'past_paper_id');
    }

    /**
     * Get the student who made this attempt.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get all answers for this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(PastPaperAttemptAnswer::class, 'attempt_id');
    }

    /**
     * Scope: Get submitted attempts only.
     */
    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'auto_submitted']);
    }

    /**
     * Get stats for a subject for a student.
     * Returns: attempts_count, last_percent, avg_percent
     */
    public static function getSubjectStats($studentId, $subjectId, $stream, $category = null)
    {
        $attempts = self::query()
            ->where('student_id', $studentId)
            ->whereHas('pastPaper', function ($q) use ($subjectId, $stream, $category) {
                $q->where('subject_id', $subjectId)->where('stream', $stream);
                if ($category) {
                    $q->where('category', $category);
                }
            })
            ->submitted()
            ->orderBy('created_at', 'desc');

        $count = $attempts->count();
        $lastPercent = $attempts->first()?->percentage ?? 0;
        $avgPercent = $count > 0
            ? round($attempts->avg('percentage'), 2)
            : 0;

        return [
            'attempts_count' => $count,
            'last_percent' => $lastPercent,
            'avg_percent' => $avgPercent,
        ];
    }}