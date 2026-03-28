<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\PastPaper;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Existing exam stats
        $examsStats = Exam::selectRaw('
            COUNT(*) as total_exams,
            SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) as published_exams,
            SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) as draft_exams
        ')->first();

        $attemptsStats = ExamAttempt::selectRaw('
            COUNT(*) as total_attempts,
            SUM(CASE WHEN status = "submitted" THEN 1 ELSE 0 END) as submitted_count,
            SUM(CASE WHEN status = "auto_submitted" THEN 1 ELSE 0 END) as auto_submitted_count
        ')->first();

        // === ADMIN ANALYTICS (Always computed, independent of homepage settings) ===
        
        // Platform counts
        $studentsCount = User::where('role', 'student')->count();
        $teachersCount = User::where('role', 'teacher')->count();
        $examsCount = Exam::count();
        $pastPapersCount = PastPaper::where('category', '!=', 'free_style')->count();
        $freeStylePapersCount = PastPaper::where('category', 'free_style')->count();

        return view('admin.dashboard', [
            // Exam stats
            'totalExams' => $examsStats->total_exams ?? 0,
            'publishedExams' => $examsStats->published_exams ?? 0,
            'draftExams' => $examsStats->draft_exams ?? 0,
            'totalAttempts' => $attemptsStats->total_attempts ?? 0,
            'submittedAttempts' => $attemptsStats->submitted_count ?? 0,
            'autoSubmittedAttempts' => $attemptsStats->auto_submitted_count ?? 0,
            
            // Old stat cards (for backward compatibility with existing dashboard cards)
            'totalStudents' => $studentsCount,
            'totalTeachers' => $teachersCount,
            'totalExamsCount' => $examsCount,
            'totalPastPapers' => $pastPapersCount,
            'totalFreeStylePapers' => $freeStylePapersCount,
        ]);
    }

}
