<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'exam_uid',
        'teacher_name',
        'exam_code',
        'description',
        'instructions',
        'duration_minutes',

        // ✅ NEW: question pool settings
        'question_limit',      // e.g. 40
        'selection_mode',      // all | first_n | random_n | manual
        'option_count',        // 3, 4, or 5 (MCQ answer count)

        // existing
        'starts_at',
        'ends_at',
        'question_mode',       // ordered | shuffled
        'status',
        'results_published',
        'created_by',

        // images
        'image_1',
        'image_2',
        'image_3',

        'custom_success_popup_enabled',
'custom_success_popup_title',
'custom_success_popup_message',
'custom_success_popup_link',
'custom_success_popup_show_copy',


    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'results_published' => 'boolean',
        'question_limit' => 'integer',
        'option_count' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function attempts()
    {
        return $this->hasMany(\App\Models\ExamAttempt::class);
    }
}
