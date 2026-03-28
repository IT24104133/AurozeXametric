<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PastPaper;
use App\Models\PastPaperSubject;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class StudentPastPaperController extends Controller
{
    /**
     * Show home page with 3 stream cards
     */
    public function home()
    {
        $streams = getStreams();
        $streamData = [];

        foreach ($streams as $stream) {
            $count = PastPaperSubject::where('stream', $stream)
                ->whereHas('pastPapers', function ($query) {
                    $query->where('status', 'published');
                })
                ->count();

            $streamData[$stream] = $count;
        }

        return view('student.past_papers.home', compact('streamData'));
    }

    /**
     * Show subjects for a specific stream
     */
    public function streams($stream)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        $studentId = Auth::id();

        // Get Education Department subjects
        $eduSubjects = PastPaperSubject::where('stream', $stream)
            ->whereHas('pastPapers', function ($query) {
                $query->where('status', 'published')
                    ->where('category', 'edu_department');
            })
            ->with(['pastPapers' => function ($query) {
                $query->where('status', 'published')
                    ->where('category', 'edu_department')
                    ->orderBy('year', 'desc')
                    ->orderBy('title');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($subject) use ($studentId, $stream) {
                $stats = \App\Models\PastPaperAttempt::getSubjectStats($studentId, $subject->id, $stream, 'edu_department');
                $subject->attempts_count = $stats['attempts_count'];
                $subject->last_percent = $stats['last_percent'];
                $subject->avg_percent = $stats['avg_percent'];
                return $subject;
            });

        // Get Free Style subjects
        $freeSubjects = PastPaperSubject::where('stream', $stream)
            ->whereHas('pastPapers', function ($query) {
                $query->where('status', 'published')
                    ->where('category', 'free_style');
            })
            ->with(['pastPapers' => function ($query) {
                $query->where('status', 'published')
                    ->where('category', 'free_style')
                    ->orderBy('year', 'desc')
                    ->orderBy('title');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($subject) use ($studentId, $stream) {
                $stats = \App\Models\PastPaperAttempt::getSubjectStats($studentId, $subject->id, $stream, 'free_style');
                $subject->attempts_count = $stats['attempts_count'];
                $subject->last_percent = $stats['last_percent'];
                $subject->avg_percent = $stats['avg_percent'];
                return $subject;
            });

        return view('student.past_papers.streams', compact('stream', 'eduSubjects', 'freeSubjects'));
    }

    /**
     * Show papers for a specific subject and stream
     */
    public function subject($stream, PastPaperSubject $subject)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure subject belongs to stream
        if ($subject->stream !== $stream) {
            abort(404);
        }

        $hasEdu = $subject->pastPapers()
            ->where('stream', $stream)
            ->where('status', 'published')
            ->where('category', 'edu_department')
            ->exists();

        $source = $hasEdu ? 'education' : 'free';

        return redirect()->route('student.past_papers.subject.papers', [
            'stream' => $stream,
            'subject' => $subject->id,
            'source' => $source,
        ]);
    }

    /**
     * Show papers for a specific subject + source (education/free)
     */
    public function showSubjectPapers($stream, PastPaperSubject $subject, $source)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure subject belongs to stream
        if ($subject->stream !== $stream) {
            abort(404);
        }

        $source = strtolower($source);
        if (!in_array($source, ['education', 'free'])) {
            abort(404);
        }

        $category = $source === 'education' ? 'edu_department' : 'free_style';
        $sourceLabel = $source === 'education' ? 'Education Department' : 'Free Style';

        $studentId = Auth::id();

        $papers = $subject->pastPapers()
            ->where('stream', $stream)
            ->where('status', 'published')
            ->where('category', $category)
            ->orderBy('year', 'desc')
            ->orderBy('title')
            ->withCount('questions')
            ->withCount(['attempts as attempts_count' => function ($q) use ($studentId) {
                $q->where('student_id', $studentId)
                  ->where('status', 'submitted');
            }])
            ->with(['attempts' => function ($q) use ($studentId) {
                $q->where('student_id', $studentId)
                  ->where('status', 'submitted')
                  ->latest('ended_at');
            }])
            ->get()
            ->map(function ($paper) {
                $paper->lastAttempt = $paper->attempts->first();
                return $paper;
            });

        $count = $papers->count();
        $badgeLabel = $count > 0 ? ($count . ' ' . Str::plural('Paper', $count)) : 'Coming Soon';

        return view('student.past_papers.subject', compact('stream', 'subject', 'papers', 'sourceLabel', 'badgeLabel'));
    }

    /**
     * Calculate subject progress percentage for a student (legacy method)
     */
    private function calculateSubjectProgress(int $subjectId, int $studentId): float
    {
        $papers = PastPaper::where('subject_id', $subjectId)
            ->where('status', 'published')
            ->with(['attempts' => function ($q) use ($studentId) {
                $q->where('student_id', $studentId)
                  ->where('status', 'submitted')
                  ->select('past_paper_id', 'percentage');
            }])
            ->get();

        if ($papers->isEmpty()) {
            return 0;
        }

        $totalPercentage = 0;
        $paperCount = $papers->count();

        foreach ($papers as $paper) {
            $bestPercentage = $paper->attempts->max('percentage') ?? 0;
            $totalPercentage += $bestPercentage;
        }

        return $paperCount > 0 ? round($totalPercentage / $paperCount, 1) : 0;
    }
}
