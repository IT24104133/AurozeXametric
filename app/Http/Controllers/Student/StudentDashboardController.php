<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentWallet;
use App\Models\CoinTransaction;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // ✅ Published exams
        $publishedExamsQuery = Exam::query()
            ->where('status', 'published')
            ->orderByDesc('id');

        $totalPublishedExams = (clone $publishedExamsQuery)->count();

        // ✅ Attempts stats
        $attemptedCount = ExamAttempt::where('user_id', $userId)
            ->whereIn('status', ['in_progress', 'submitted', 'auto_submitted'])
            ->count();

        $submittedCount = ExamAttempt::where('user_id', $userId)
            ->where('status', 'submitted')
            ->count();

        $autoSubmittedCount = ExamAttempt::where('user_id', $userId)
            ->where('status', 'auto_submitted')
            ->count();

        $publishedResultsCount = ExamAttempt::where('user_id', $userId)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->whereHas('exam', fn($q) => $q->where('results_published', true))
            ->count();

        // ✅ Continue attempt
        $continueAttempt = ExamAttempt::where('user_id', $userId)
            ->where('status', 'in_progress')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->with('exam')
            ->latest('id')
            ->first();

        // ✅ Latest published results
        $latestResults = ExamAttempt::where('user_id', $userId)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->whereHas('exam', fn($q) => $q->where('results_published', true))
            ->with('exam')
            ->orderByDesc('submitted_at')
            ->limit(3)
            ->get();

        /**
         * ✅ Available Exams (shared eligibility rules for count + list)
         */
        $availableExamsQuery = Exam::query()
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->whereDoesntHave('attempts', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereIn('status', ['in_progress', 'submitted', 'auto_submitted']);
            });

        $availableCount = (clone $availableExamsQuery)->count();

        $availableExamsList = (clone $availableExamsQuery)
            ->with(['attempts' => function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->select('id', 'exam_id', 'user_id', 'status', 'submitted_at', 'ends_at');
            }])
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->map(function ($exam) {
                $attempt = $exam->attempts->first();
                $exam->my_attempt = $attempt; // attach for blade
                return $exam;
            });

        // Get wallet info for daily coin tracking
        $wallet = StudentWallet::where('user_id', $userId)->first();
        
        // Get today's coins and remaining cap
        $todayCoins = CoinTransaction::whereDate('earned_on', today())
            ->where('user_id', $userId)
            ->sum('coins');
        $remainingCoinsToday = 50 - $todayCoins;

        return view('student.dashboard', [
            'totalPublishedExams' => $totalPublishedExams,
            'attemptedCount' => $attemptedCount,
            'submittedCount' => $submittedCount,
            'autoSubmittedCount' => $autoSubmittedCount,
            'publishedResultsCount' => $publishedResultsCount,
            'continueAttempt' => $continueAttempt,
            'latestResults' => $latestResults,
            'availableExamsList' => $availableExamsList,
            'availableCount' => $availableCount,
            'todayCoins' => $todayCoins,
            'remainingCoinsToday' => $remainingCoinsToday,
        ]);
    }
}
