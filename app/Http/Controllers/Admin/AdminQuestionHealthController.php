<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PastPaperQuestion;
use App\Models\PastPaper;
use App\Models\PastPaperSubject;
use Illuminate\Support\Facades\DB;

class AdminQuestionHealthController extends Controller
{
    public function index()
    {
        // A) Global Question Distribution (all published past paper questions)
        $globalStats = $this->getGlobalDistribution();
        
        // B) By Stream + Subject
        $streamSubjectStats = $this->getStreamSubjectBreakdown();
        
        // C) Coverage Analysis
        $coverageAnalysis = $this->getCoverageAnalysis();
        
        // D) Fix Suggestions
        $suggestions = $this->generateSuggestions($streamSubjectStats, $coverageAnalysis);
        
        return view('admin.question_health.index', compact(
            'globalStats',
            'streamSubjectStats',
            'coverageAnalysis',
            'suggestions'
        ));
    }
    
    private function getGlobalDistribution()
    {
        // Count all past paper questions by difficulty
        $questions = PastPaperQuestion::select('difficulty', DB::raw('COUNT(*) as count'))
            ->groupBy('difficulty')
            ->get();
        
        $total = $questions->sum('count');
        $easy = 0;
        $medium = 0;
        $hard = 0;
        
        foreach ($questions as $q) {
            $diff = strtolower($q->difficulty);
            if (in_array($diff, ['easy', 'e'])) {
                $easy += $q->count;
            } elseif (in_array($diff, ['medium', 'm'])) {
                $medium += $q->count;
            } elseif (in_array($diff, ['hard', 'h'])) {
                $hard += $q->count;
            }
        }
        
        return [
            'total' => $total,
            'easy' => $easy,
            'medium' => $medium,
            'hard' => $hard,
            'easy_percent' => $total > 0 ? round(($easy / $total) * 100, 1) : 0,
            'medium_percent' => $total > 0 ? round(($medium / $total) * 100, 1) : 0,
            'hard_percent' => $total > 0 ? round(($hard / $total) * 100, 1) : 0,
        ];
    }
    
    private function getStreamSubjectBreakdown()
    {
        $subjects = PastPaperSubject::with(['pastPapers.questions'])->get();
        
        $breakdown = [];
        
        foreach ($subjects as $subject) {
            // Get all questions for this subject across all past papers
            $allQuestions = collect();
            foreach ($subject->pastPapers as $paper) {
                $allQuestions = $allQuestions->merge($paper->questions);
            }
            
            $total = $allQuestions->count();
            $easy = $allQuestions->filter(fn($q) => in_array(strtolower($q->difficulty), ['easy', 'e']))->count();
            $medium = $allQuestions->filter(fn($q) => in_array(strtolower($q->difficulty), ['medium', 'm']))->count();
            $hard = $allQuestions->filter(fn($q) => in_array(strtolower($q->difficulty), ['hard', 'h']))->count();
            
            $stream = $subject->stream ?: 'Unknown';
            
            if (!isset($breakdown[$stream])) {
                $breakdown[$stream] = [];
            }
            
            $breakdown[$stream][] = [
                'subject' => $subject->name,
                'subject_id' => $subject->id,
                'total' => $total,
                'easy' => $easy,
                'medium' => $medium,
                'hard' => $hard,
                'easy_percent' => $total > 0 ? round(($easy / $total) * 100, 1) : 0,
                'medium_percent' => $total > 0 ? round(($medium / $total) * 100, 1) : 0,
                'hard_percent' => $total > 0 ? round(($hard / $total) * 100, 1) : 0,
                'warning' => $easy < 10 || $medium < 10 || $hard < 10,
            ];
        }
        
        return $breakdown;
    }
    
    private function getCoverageAnalysis()
    {
        // Get all published past papers (education_department and free_style)
        $papers = PastPaper::with(['subject', 'questions'])
            ->whereIn('category', ['education_department', 'free_style'])
            ->where('status', 'published')
            ->get();
        
        $analysis = [];
        
        foreach ($papers as $paper) {
            $subject = $paper->subject;
            if (!$subject) continue;
            
            // Get available questions for this subject
            $availableQuestions = PastPaperQuestion::whereHas('pastPaper', function($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            })->get();
            
            $availableEasy = $availableQuestions->filter(fn($q) => in_array(strtolower($q->difficulty), ['easy', 'e']))->count();
            $availableMedium = $availableQuestions->filter(fn($q) => in_array(strtolower($q->difficulty), ['medium', 'm']))->count();
            $availableHard = $availableQuestions->filter(fn($q) => in_array(strtolower($q->difficulty), ['hard', 'h']))->count();
            
            // Required counts
            $requiredEasy = $paper->count_e ?? 0;
            $requiredMedium = $paper->count_m ?? 0;
            $requiredHard = $paper->count_h ?? 0;
            $requiredTotal = $paper->total_questions ?? 0;
            
            // Determine status
            $easyStatus = $this->getStatus($availableEasy, $requiredEasy);
            $mediumStatus = $this->getStatus($availableMedium, $requiredMedium);
            $hardStatus = $this->getStatus($availableHard, $requiredHard);
            
            $overallStatus = 'ok';
            if ($easyStatus === 'fail' || $mediumStatus === 'fail' || $hardStatus === 'fail') {
                $overallStatus = 'fail';
            } elseif ($easyStatus === 'low' || $mediumStatus === 'low' || $hardStatus === 'low') {
                $overallStatus = 'low';
            }
            
            $stream = $paper->stream ?: 'Unknown';
            
            if (!isset($analysis[$stream])) {
                $analysis[$stream] = [];
            }
            
            $analysis[$stream][] = [
                'paper_title' => $paper->title,
                'subject' => $subject->name,
                'category' => $paper->category,
                'required_total' => $requiredTotal,
                'required_easy' => $requiredEasy,
                'required_medium' => $requiredMedium,
                'required_hard' => $requiredHard,
                'available_easy' => $availableEasy,
                'available_medium' => $availableMedium,
                'available_hard' => $availableHard,
                'status' => $overallStatus,
                'easy_status' => $easyStatus,
                'medium_status' => $mediumStatus,
                'hard_status' => $hardStatus,
            ];
        }
        
        return $analysis;
    }
    
    private function getStatus($available, $required)
    {
        if ($required == 0) return 'ok';
        
        $ratio = $available / $required;
        
        if ($ratio >= 1.5) return 'ok';  // 150% or more
        if ($ratio >= 1.0) return 'low'; // 100-149%
        return 'fail';                    // Less than 100%
    }
    
    private function generateSuggestions($streamSubjectStats, $coverageAnalysis)
    {
        $suggestions = [];
        
        // Suggestions from subject breakdown
        foreach ($streamSubjectStats as $stream => $subjects) {
            foreach ($subjects as $subjectData) {
                if ($subjectData['warning']) {
                    if ($subjectData['easy'] < 10) {
                        $needed = 10 - $subjectData['easy'];
                        $suggestions[] = "Add {$needed} Easy questions for {$subjectData['subject']} ({$stream})";
                    }
                    if ($subjectData['medium'] < 10) {
                        $needed = 10 - $subjectData['medium'];
                        $suggestions[] = "Add {$needed} Medium questions for {$subjectData['subject']} ({$stream})";
                    }
                    if ($subjectData['hard'] < 10) {
                        $needed = 10 - $subjectData['hard'];
                        $suggestions[] = "Add {$needed} Hard questions for {$subjectData['subject']} ({$stream})";
                    }
                }
            }
        }
        
        // Suggestions from coverage analysis
        foreach ($coverageAnalysis as $stream => $papers) {
            foreach ($papers as $paperData) {
                if ($paperData['status'] === 'fail' || $paperData['status'] === 'low') {
                    if ($paperData['easy_status'] !== 'ok') {
                        $needed = $paperData['required_easy'] - $paperData['available_easy'];
                        if ($needed > 0) {
                            $suggestions[] = "Add {$needed} Easy questions for {$paperData['subject']} - {$paperData['paper_title']}";
                        }
                    }
                    if ($paperData['medium_status'] !== 'ok') {
                        $needed = $paperData['required_medium'] - $paperData['available_medium'];
                        if ($needed > 0) {
                            $suggestions[] = "Add {$needed} Medium questions for {$paperData['subject']} - {$paperData['paper_title']}";
                        }
                    }
                    if ($paperData['hard_status'] !== 'ok') {
                        $needed = $paperData['required_hard'] - $paperData['available_hard'];
                        if ($needed > 0) {
                            $suggestions[] = "Add {$needed} Hard questions for {$paperData['subject']} - {$paperData['paper_title']}";
                        }
                    }
                }
            }
        }
        
        return array_unique($suggestions);
    }
}
