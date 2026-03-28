<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;

class ExamResultController extends Controller
{
    public function index(Exam $exam)
    {
        // Get all attempts for this exam with student info
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with('user') // requires relation in ExamAttempt model
            ->orderByDesc('score')
            ->orderBy('submitted_at')
            ->get();

        return view('admin.exams.results', compact('exam', 'attempts'));
    }
}
