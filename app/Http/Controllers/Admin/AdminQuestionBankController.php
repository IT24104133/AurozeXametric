<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PastPaper;
use App\Models\PastPaperQuestion;
use App\Models\PastPaperSubject;
use Illuminate\Support\Facades\DB;

class AdminQuestionBankController extends Controller
{
    /**
     * Show question bank health overview
     */
    public function health()
    {
        // Get all subjects with their bank stats
        $subjects = PastPaperSubject::query()
            ->withCount('pastPapers')
            ->get()
            ->map(function ($subject) {
                // Get all questions in edu_department papers for this subject
                $bankQuestions = PastPaperQuestion::query()
                    ->whereHas('pastPaper', function ($q) use ($subject) {
                        $q->where('subject_id', $subject->id)
                          ->where('category', 'edu_department')
                          ->where('status', 'published');
                    })
                    ->selectRaw('difficulty, COUNT(*) as count')
                    ->groupBy('difficulty')
                    ->get()
                    ->keyBy('difficulty');

                $totalBank = $bankQuestions->sum('count');
                $eCount = $bankQuestions->get('E')?->count ?? 0;
                $mCount = $bankQuestions->get('M')?->count ?? 0;
                $hCount = $bankQuestions->get('H')?->count ?? 0;

                // Get free style paper config if exists
                $freeStylePaper = $subject->pastPapers()
                    ->where('category', 'free_style')
                    ->where('status', 'published')
                    ->first();

                $hasWarning = false;
                $warningMessage = null;

                if ($freeStylePaper) {
                    // Check if required counts exceed available
                    $countE = $freeStylePaper->count_e ?? 12;
                    $countM = $freeStylePaper->count_m ?? 18;
                    $countH = $freeStylePaper->count_h ?? 10;

                    if ($eCount < $countE) {
                        $hasWarning = true;
                        $warningMessage = "Easy: Need {$countE}, have {$eCount}";
                    } elseif ($mCount < $countM) {
                        $hasWarning = true;
                        $warningMessage = "Medium: Need {$countM}, have {$mCount}";
                    } elseif ($hCount < $countH) {
                        $hasWarning = true;
                        $warningMessage = "Hard: Need {$countH}, have {$hCount}";
                    }
                }

                $subject->bank_total = $totalBank;
                $subject->bank_e = $eCount;
                $subject->bank_m = $mCount;
                $subject->bank_h = $hCount;
                $subject->has_warning = $hasWarning;
                $subject->warning_message = $warningMessage;

                return $subject;
            });

        return view('admin.question_bank.health', compact('subjects'));
    }
}
