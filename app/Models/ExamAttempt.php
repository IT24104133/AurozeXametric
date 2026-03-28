<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'status',
        'started_at',
        'ends_at',
        'submitted_at',
        'score',
        'total_questions',
        'question_order',
    ];

    protected $casts = [
    'question_order' => 'array',
    'started_at' => 'datetime',
    'ends_at' => 'datetime',
    'submitted_at' => 'datetime',
];

    public function exam()
    {
        return $this->belongsTo(\App\Models\Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAttemptAnswer::class, 'attempt_id');
    }
    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}
