<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = ['attempt_id','question_id','selected_option','answered_at'];

    protected $casts = [
        'answered_at' => 'datetime',
    ];
}
